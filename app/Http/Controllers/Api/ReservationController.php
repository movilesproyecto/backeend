<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Department;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Crear una nueva reserva con validaciones
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'reservation_date' => 'required|date|after:today',
            'reservation_time' => 'required|date_format:H:i',
            'duration' => 'required|string',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $department = Department::findOrFail($data['department_id']);

        // Validar: no permitir 2 reservas del mismo usuario en el mismo departamento en la misma fecha
        $existingReservation = Reservation::where('user_id', $user->id)
            ->where('department_id', $data['department_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingReservation) {
            return response()->json([
                'message' => 'Ya tienes una reserva confirmada para este departamento en esta fecha.',
                'success' => false
            ], 422);
        }

        // Validar: no permitir choque de horarios (misma hora y departamento en la misma fecha)
        $conflictingReservation = Reservation::where('department_id', $data['department_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->where('reservation_time', $data['reservation_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($conflictingReservation) {
            return response()->json([
                'message' => 'Este departamento ya está reservado en esa fecha y hora.',
                'success' => false
            ], 422);
        }

        // Calcular amount basado en el precio del departamento
        $amount = $department->price_per_night ?? 50;

        // Crear la reserva
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'department_id' => $data['department_id'],
            'reservation_date' => $data['reservation_date'],
            'reservation_time' => $data['reservation_time'],
            'duration' => $data['duration'],
            'amount' => $amount,
            'payment_method' => $data['payment_method'] ?? null,
            'payment_date' => $data['payment_method'] ? now()->toDateString() : null,
            'status' => $data['payment_method'] ? 'confirmed' : 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        // Enviar notificación
        NotificationController::sendNotification(
            $user->id,
            'Reserva ' . ($reservation->status === 'confirmed' ? 'Confirmada' : 'Pendiente'),
            $reservation->status === 'confirmed'
                ? 'Tu reserva en ' . $department->name . ' ha sido confirmada'
                : 'Tu reserva en ' . $department->name . ' está pendiente de confirmación',
            $reservation->status === 'confirmed' ? 'success' : 'info',
            $department->id,
            'reservation',
            $reservation->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'reservation' => $reservation->load(['department', 'user']),
        ], 201);
    }

    /**
     * Obtener todas las reservas del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['department:id,name,price_per_night,images', 'user:id,name,email'])
            ->orderBy('reservation_date', 'desc')
            ->get();

        return response()->json($reservations->toArray());
    }

    /**
     * Obtener una reserva específica
     */
    public function show(Reservation $reservation, Request $request)
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($reservation->load(['department', 'user']));
    }

    /**
     * Cancelar una reserva
     */
    public function destroy(Reservation $reservation, Request $request)
    {
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($reservation->status === 'cancelled') {
            return response()->json(['message' => 'Esta reserva ya fue cancelada'], 422);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Reserva cancelada']);
    }

    /**
     * Obtener horarios disponibles para un departamento en una fecha
     */
    public function availableSlots(Request $request)
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'date' => 'required|date|after:today',
        ]);

        // Horarios disponibles de 9:00 a 18:00 en intervalos de 1 hora
        $availableSlots = [
            '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        // Obtener reservas confirmadas/pendientes para este departamento y fecha
        $bookedSlots = Reservation::where('department_id', $data['department_id'])
            ->where('reservation_date', $data['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('reservation_time')
            ->toArray();

        // Filtrar horarios disponibles
        $available = array_filter($availableSlots, function($slot) use ($bookedSlots) {
            return !in_array($slot, $bookedSlots);
        });

        return response()->json([
            'success' => true,
            'available_slots' => array_values($available),
            'booked_slots' => $bookedSlots,
            'date' => $data['date'],
        ]);
    }

    /**
     * Actualizar el estado de una reserva (solo para administradores)
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        // Verificar permisos: solo administradores pueden cambiar estados
        $userRole = strtolower($user->role ?? 'user');
        if (!in_array($userRole, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar el estado de las reservas.',
                'success' => false
            ], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        // Validar transiciones de estado permitidas
        $currentStatus = $reservation->status;
        $newStatus = $data['status'];

        // Solo permitir ciertas transiciones
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [], // No se puede cambiar una vez completada
            'cancelled' => [], // No se puede cambiar una vez cancelada
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            return response()->json([
                'message' => "No se puede cambiar el estado de '{$currentStatus}' a '{$newStatus}'.",
                'success' => false
            ], 422);
        }

        // Actualizar el estado
        $reservation->update(['status' => $newStatus]);

        return response()->json([
            'message' => 'Estado de la reserva actualizado correctamente.',
            'success' => true,
            'reservation' => $reservation->fresh()
        ]);
    }
}

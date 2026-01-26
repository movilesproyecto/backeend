<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importante para tipado
use Illuminate\Support\Facades\Auth; // Importante para el editor

class ReservationController extends Controller
{
    /**
     * Crear una nueva reserva con validaciones
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'department_id'    => 'required|exists:departments,id',
            'reservation_date' => 'required|date|after:today',
            'reservation_time' => 'required|date_format:H:i',
            'duration'         => 'required|string',
            'payment_method'   => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $department = Department::findOrFail($data['department_id']);

        // 1. Validar reserva existente del usuario
        $exists = Reservation::where('user_id', $user->id)
            ->where('department_id', $data['department_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya tienes una reserva confirmada para este departamento en esta fecha.',
                'success' => false
            ], 422);
        }

        // 2. Validar choque de horarios
        $conflict = Reservation::where('department_id', $data['department_id'])
            ->where('reservation_date', $data['reservation_date'])
            ->where('reservation_time', $data['reservation_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Este departamento ya está reservado en esa fecha y hora.',
                'success' => false
            ], 422);
        }

        $amount = $department->price_per_night ?? 50;
        $paymentMethod = $data['payment_method'] ?? null;
        $status = $paymentMethod ? 'confirmed' : 'pending';

        $reservation = Reservation::create([
            'user_id'          => $user->id,
            'department_id'    => $data['department_id'],
            'reservation_date' => $data['reservation_date'],
            'reservation_time' => $data['reservation_time'],
            'duration'         => $data['duration'],
            'amount'           => $amount,
            'payment_method'   => $paymentMethod,
            'payment_date'     => $paymentMethod ? now()->toDateString() : null,
            'status'           => $status,
            'notes'            => $data['notes'] ?? null,
        ]);

        // Enviar notificación (usando el método estático de NotificationController)
        NotificationController::sendNotification(
            $user->id,
            'Reserva ' . ($status === 'confirmed' ? 'Confirmada' : 'Pendiente'),
            $status === 'confirmed'
                ? "Tu reserva en {$department->name} ha sido confirmada"
                : "Tu reserva en {$department->name} está pendiente de confirmación",
            $status === 'confirmed' ? 'success' : 'info',
            $department->id,
            'reservation',
            $reservation->id
        );

        return response()->json([
            'success'     => true,
            'message'     => 'Reserva creada exitosamente',
            'reservation' => $reservation->load(['department', 'user']),
        ], 201);
    }

    /**
     * Obtener todas las reservas del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reservations = Reservation::where('user_id', $user->id)
            ->with(['department', 'user'])
            ->orderByDesc('reservation_date') // Helper Laravel
            ->paginate(12);

        return response()->json($reservations);
    }

    /**
     * Obtener una reserva específica
     */
    public function show(Reservation $reservation, Request $request): JsonResponse
    {
        if (!$this->ensureOwnership($request->user(), $reservation)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($reservation->load(['department', 'user']));
    }

    /**
     * Cancelar una reserva
     */
    public function destroy(Reservation $reservation, Request $request): JsonResponse
    {
        if (!$this->ensureOwnership($request->user(), $reservation)) {
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
    public function availableSlots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'date'          => 'required|date|after:today',
        ]);

        $availableSlots = [
            '09:00', '10:00', '11:00', '12:00', '13:00',
            '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        // Usamos pluck directamente para un código más limpio
        $bookedSlots = Reservation::where('department_id', $data['department_id'])
            ->where('reservation_date', $data['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('reservation_time')
            ->toArray();

        // Filtrado con Arrow Function (PHP 7.4+)
        $available = array_filter(
            $availableSlots,
            fn($slot) => !in_array($slot, $bookedSlots)
        );

        return response()->json([
            'success'         => true,
            'available_slots' => array_values($available),
            'booked_slots'    => $bookedSlots,
            'date'            => $data['date'],
        ]);
    }

    /**
     * Helper privado para verificar propiedad
     */
    private function ensureOwnership($user, Reservation $reservation): bool
    {
        return $reservation->user_id === $user->id;
    }
}

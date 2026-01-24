<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obtener todas las notificaciones del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $query = $user->notifications();

        // Filtrar por tipo si se proporciona
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtrar por estado de lectura
        if ($request->has('read')) {
            $read = $request->read === 'true' || $request->read === true;
            $query->where('read', $read);
        }

        $notifications = $query->latest()->paginate(15);

        return response()->json([
            'data' => $notifications->items(),
            'total' => $notifications->total(),
            'unread_count' => $user->unreadNotifications()->count(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'last_page' => $notifications->lastPage(),
            ]
        ]);
    }

    /**
     * Obtener una notificación específica
     */
    public function show(Request $request, Notification $notification)
    {
        $user = $request->user();
        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Marcar como leída al ver
        if (!$notification->read) {
            $notification->markAsRead();
        }

        return response()->json($notification);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();
        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->unreadNotifications()->update(['read' => true]);

        return response()->json([
            'message' => 'All notifications marked as read',
            'unread_count' => 0
        ]);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(Request $request, Notification $notification)
    {
        $user = $request->user();
        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Eliminar todas las notificaciones
     */
    public function deleteAll(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->notifications()->delete();

        return response()->json(['message' => 'All notifications deleted']);
    }

    /**
     * Obtener conteo de notificaciones no leídas
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Crear una notificación (función auxiliar para otras partes del sistema)
     */
    public static function sendNotification($userId, $title, $message, $type = 'info', $departmentId = null, $actionType = null, $actionId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => self::getIconForType($type),
            'department_id' => $departmentId,
            'action_type' => $actionType,
            'action_id' => $actionId,
        ]);
    }

    /**
     * Obtener el icono según el tipo de notificación
     */
    public static function getIconForType($type)
    {
        $icons = [
            'success' => 'check-circle',
            'warning' => 'exclamation-circle',
            'error' => 'times-circle',
            'info' => 'bell',
        ];
        return $icons[$type] ?? 'bell';
    }
}

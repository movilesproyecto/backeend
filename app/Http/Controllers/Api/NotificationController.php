<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Obtener todas las notificaciones del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // Usar Auth::user() ayuda al editor más que $request->user()

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Al poner el comentario @var arriba, el editor ya sabe que 'notifications()' existe
        $query = $user->notifications();

        $query->when($request->input('type'), fn($q, $type) => $q->where('type', $type));

        if ($request->has('read')) {
            $query->where('read', $request->boolean('read'));
        }

        $notifications = $query->latest()->paginate(15);

        return response()->json([
            'data'         => $notifications->items(),
            'total'        => $notifications->total(),
            'unread_count' => $user->unreadNotifications()->count(),
            'pagination'   => [
                'current_page' => $notifications->currentPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
                'last_page'    => $notifications->lastPage(),
            ]
        ]);
    }

    /**
     * Obtener una notificación específica
     */
    public function show(Request $request, Notification $notification): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$this->ensureOwnership($user, $notification)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$notification->read) {
            $notification->markAsRead();
        }

        return response()->json($notification);
    }

    /**
     * Marcar una notificación como leída
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$this->ensureOwnership($user, $notification)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification->markAsRead();

        return response()->json([
            'message'      => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // El editor ya no marcará error aquí gracias al @var
        $user->unreadNotifications()->update(['read' => true]);

        return response()->json([
            'message'      => 'All notifications marked as read',
            'unread_count' => 0
        ]);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || !$this->ensureOwnership($user, $notification)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Eliminar todas las notificaciones
     */
    public function deleteAll(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->notifications()->delete();

        return response()->json(['message' => 'All notifications deleted']);
    }

    /**
     * Obtener conteo de notificaciones no leídas
     */
    public function unreadCount(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    // --- Helpers estáticos ---

    public static function sendNotification($userId, $title, $message, $type = 'info', $departmentId = null, $actionType = null, $actionId = null)
    {
        return Notification::create([
            'user_id'       => $userId,
            'title'         => $title,
            'message'       => $message,
            'type'          => $type,
            'icon'          => self::getIconForType($type),
            'department_id' => $departmentId,
            'action_type'   => $actionType,
            'action_id'     => $actionId,
        ]);
    }

    public static function getIconForType($type)
    {
        return match ($type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-circle',
            'error'   => 'times-circle',
            'info'    => 'bell',
            default   => 'bell',
        };
    }

    /**
     * Helper privado.
     * Importante: El type hint del $user ayuda al editor aquí también.
     * @param \App\Models\User $user
     */
    private function ensureOwnership($user, Notification $notification): bool
    {
        return $notification->user_id === $user->id;
    }
}

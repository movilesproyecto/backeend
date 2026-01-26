<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importante para tipado
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Importante para el editor

class UserController extends Controller
{
    /**
     * Crear un nuevo usuario (Solo admin/superadmin)
     */
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'No tienes permisos para crear usuarios'], 403);
        }

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'nullable|in:user,admin,superadmin',
        ]);

        try {
            // Regla de negocio: Solo superadmin puede crear superadmin
            $requestedRole = $data['role'] ?? 'user';

            if ($requestedRole === 'superadmin' && strtolower($currentUser->role) !== 'superadmin') {
                return response()->json(['message' => 'Solo Superadmin puede crear usuarios Superadmin'], 403);
            }

            $newUser = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $requestedRole,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data'    => [
                    'id'    => $newUser->id,
                    'name'  => $newUser->name,
                    'email' => $newUser->email,
                    'role'  => $newUser->role,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear usuario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener todos los usuarios
     */
    public function index(): JsonResponse
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // Seleccionamos campos específicos por seguridad (evitar password, tokens, etc)
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();

        return response()->json([
            'success' => true,
            'data'    => $users
        ]);
    }

    /**
     * Cambiar rol de un usuario
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $data = $request->validate([
            'role' => 'required|in:user,admin,superadmin',
        ]);

        // Regla de negocio: Solo superadmin puede asignar superadmin
        if ($data['role'] === 'superadmin' && strtolower($currentUser->role) !== 'superadmin') {
            return response()->json(['message' => 'Solo Superadmin puede asignar rol Superadmin'], 403);
        }

        $user->update(['role' => $data['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado',
            'data'    => $user
        ]);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(User $user): JsonResponse
    {
        /** @var \App\Models\User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->isAdmin($currentUser)) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // Evitar suicidio digital (borrarse a sí mismo)
        if ($currentUser->id === $user->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado'
        ]);
    }

    /**
     * Helper privado para verificar permisos de administrador
     */
    private function isAdmin(\App\Models\User $user): bool
    {
        $role = strtolower($user->role ?? 'user');
        return in_array($role, ['admin', 'superadmin']);
    }
}

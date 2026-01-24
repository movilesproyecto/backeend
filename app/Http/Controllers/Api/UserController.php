<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Crear un nuevo usuario (Solo admin/superadmin)
     */
    public function store(Request $request)
    {
        // Verificar autenticación
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos (admin o superadmin)
        if (!in_array(strtolower($user->role ?? 'user'), ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos para crear usuarios'], 403);
        }

        // Validar datos
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:user,admin,superadmin',
        ]);

        try {
            // Solo superadmin puede crear superadmin
            if ($data['role'] === 'superadmin' && strtolower($user->role) !== 'superadmin') {
                return response()->json([
                    'message' => 'Solo Superadmin puede crear usuarios Superadmin'
                ], 403);
            }

            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'user',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'role' => $newUser->role,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los usuarios
     */
    public function index()
    {
        // Verificar autenticación
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos
        if (!in_array(strtolower($user->role ?? 'user'), ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Cambiar rol de un usuario
     */
    public function updateRole(Request $request, User $user)
    {
        // Verificar autenticación
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos
        if (!in_array(strtolower($authUser->role ?? 'user'), ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        $data = $request->validate([
            'role' => 'required|in:user,admin,superadmin',
        ]);

        // Solo superadmin puede asignar superadmin
        if ($data['role'] === 'superadmin' && strtolower($authUser->role) !== 'superadmin') {
            return response()->json([
                'message' => 'Solo Superadmin puede asignar rol Superadmin'
            ], 403);
        }

        $user->update(['role' => $data['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado',
            'data' => $user
        ]);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy(User $user)
    {
        // Verificar autenticación
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos
        if (!in_array(strtolower($authUser->role ?? 'user'), ['admin', 'superadmin'])) {
            return response()->json(['message' => 'No tienes permisos'], 403);
        }

        // No permitir eliminarse a sí mismo
        if ($authUser->id === $user->id) {
            return response()->json([
                'message' => 'No puedes eliminar tu propia cuenta'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado'
        ]);
    }
}

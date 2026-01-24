<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; // 1. Importar para tipado estricto

class AuthController extends Controller
{
    // 2. Agregar el tipo de retorno ': JsonResponse' ayuda al IDE y previene errores
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6', // Considera agregar 'confirmed' si tu app envía password_confirmation
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']), // 3. IMPORTANTE: Siempre guardar emails en minúsculas
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 3. Normalizar email también en el login para evitar errores de usuario (Ej: User@GMAIL.com)
        $credentials['email'] = strtolower($credentials['email']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Recuperar usuario autenticado
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 4. OPCIONAL: Borrar tokens anteriores del mismo dispositivo para no llenar la BD
        // $user->tokens()->where('name', 'mobile')->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        // El uso de ?-> (null safe operator) está perfecto aquí
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Masculino,Femenino,Otro',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string',
        ]);

        // Normalizar email a minúsculas
        if (isset($data['email']) && $data['email']) {
            $data['email'] = strtolower($data['email']);
        }

        // Validar contraseña actual si se va a cambiar
        if ((isset($data['password']) && $data['password'])) {
            if (!isset($data['current_password']) || !$data['current_password']) {
                return response()->json(['message' => 'Current password is required to change password'], 422);
            }
            if (!Hash::check($data['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 422);
            }
            // Hash la nueva contraseña
            $data['password'] = Hash::make($data['password']);
        }

        // Limpiar datos que no pertenecen al modelo
        unset($data['current_password'], $data['password_confirmation']);

        // Filtrar valores nulos o vacíos antes de actualizar
        $dataToUpdate = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            $user->update($dataToUpdate);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

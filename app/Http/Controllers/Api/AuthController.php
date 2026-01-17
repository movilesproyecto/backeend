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
}

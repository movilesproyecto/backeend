<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Asegúrate de mantener esta importación
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials['email'] = strtolower($credentials['email']);

        // CAMBIO: Usamos Auth::attempt en lugar de auth()->attempt
        // Esto elimina el error visual de Intelephense (P1013)
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        /** @var \App\Models\User $user */
        // CAMBIO: Usamos Auth::user() para que el editor reconozca el objeto
        $user = Auth::user();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
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
            'name'                  => 'nullable|string|max:255',
            'email'                 => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'                 => 'nullable|string|max:20',
            'gender'                => 'nullable|string|in:Masculino,Femenino,Otro',
            'department'            => 'nullable|string|max:255',
            'bio'                   => 'nullable|string|max:500',
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|string',
        ]);

        if (!empty($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return response()->json(['message' => 'Current password is required to change password'], 422);
            }

            if (!Hash::check($data['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 422);
            }

            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password'], $data['password_confirmation']);

        $dataToUpdate = array_filter($data, fn($value) => $value !== null && $value !== '');

        try {
            $user->update($dataToUpdate);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user'    => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating profile: ' . $e->getMessage(),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}

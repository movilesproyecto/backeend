<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importante para tipado
use App\Models\Review;
use App\Models\Department;
use Illuminate\Support\Facades\Auth; // Importante para el editor

class ReviewController extends Controller
{
    /**
     * Listar reseñas de un departamento (público)
     */
    public function index(Request $request, string $departmentId): JsonResponse
    {
        // Asegurar que per_page sea al menos 1
        $perPage = max(1, (int) $request->query('per_page', 20));

        $reviews = Review::where('department_id', $departmentId)
            ->with('user') // Asegúrate de que la relación 'user' exista en el modelo Review
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Crear una reseña para un departamento (autenticado)
     */
    public function store(Request $request, string $departmentId): JsonResponse
    {
        /** @var \App\Models\Department|null $dept */
        $dept = Department::find($departmentId);

        if (!$dept) {
            return response()->json(['message' => 'Departamento no encontrado.'], 404);
        }

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // Auth::id() devuelve el ID o null si no hay usuario,
        // reemplazando limpiamente la lógica "$user ? $user->id : null"
        $review = Review::create([
            'department_id' => $departmentId,
            'user_id'       => Auth::id(),
            'stars'         => $data['rating'],
            'comment'       => $data['comment'] ?? null,
        ]);

        // Recalcular el rating del departamento (promedio simple)
        // Mantenemos el try-catch para evitar que un error de cálculo falle la request
        try {
            $avg = Review::where('department_id', $departmentId)->avg('stars');

            // Nota: En tu DepartmentController anterior usabas 'rating_avg'.
            // Aquí usas 'rating'. He mantenido 'rating' para respetar este archivo,
            // pero verifica que coincida con tu base de datos.
            $dept->rating = round((float)$avg, 2);
            $dept->save();
        } catch (\Throwable $e) {
            // Silenciosamente ignorado como en el código original
        }

        return response()->json(['success' => true, 'review' => $review], 201);
    }
}

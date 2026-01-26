<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Importante para tipado
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Importante para el editor

class ImageController extends Controller
{
    /**
     * Obtener todas las imágenes de un departamento
     */
    public function index(Department $department): JsonResponse
    {
        $images = $department->images()
            ->select('id', 'department_id', 'file_name', 'file_path', 'file_size', 'mime_type', 'is_primary', 'created_at')
            ->orderByDesc('is_primary') // Helper de Laravel más limpio
            ->orderBy('created_at')
            ->get()
            ->map(fn($image) => [
                'id'           => $image->id,
                'departmentId' => $image->department_id,
                'fileName'     => $image->file_name,
                'url'          => url('/storage/' . $image->file_path),
                'fileSize'     => $image->file_size,
                'mimeType'     => $image->mime_type,
                'isPrimary'    => (bool) $image->is_primary,
                'createdAt'    => $image->created_at->toIso8601String(),
            ]);

        return response()->json([
            'data'  => $images,
            'total' => $images->count(),
        ]);
    }

    /**
     * Cargar una o múltiples imágenes
     */
    public function store(Request $request, Department $department): JsonResponse
    {
        $this->authorize('update', $department);

        $request->validate([
            'images'       => 'required|array|min:1|max:10',
            'images.*'     => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_primary'   => 'nullable|array',
            'is_primary.*' => 'boolean',
        ]);

        $user = Auth::user();
        $uploadedImages = [];

        // Obtenemos flags de primary de forma segura
        $primaryFlags = $request->input('is_primary', []);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                try {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    $path = $file->storeAs(
                        'departments/' . $department->id,
                        $fileName,
                        'public'
                    );

                    // Lógica para determinar si es primaria
                    $isPrimaryInput = isset($primaryFlags[$index]) && filter_var($primaryFlags[$index], FILTER_VALIDATE_BOOLEAN);

                    // Si es la primera imagen absoluta del departamento, forzar como primaria
                    $isFirstImage = $index === 0 && $department->images()->doesntExist();

                    $isPrimary = $isPrimaryInput || $isFirstImage;

                    // Si esta es primaria, quitar el flag a las demás
                    if ($isPrimary) {
                        $department->images()->update(['is_primary' => false]);
                    }

                    $image = Image::create([
                        'department_id' => $department->id,
                        'file_path'     => $path,
                        'file_name'     => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getMimeType(),
                        'uploaded_by'   => $user->id,
                        'is_primary'    => $isPrimary,
                    ]);

                    $uploadedImages[] = [
                        'id'        => $image->id,
                        'fileName'  => $image->file_name,
                        'url'       => url('/storage/' . $image->file_path),
                        'fileSize'  => $image->file_size,
                        'mimeType'  => $image->mime_type,
                        'isPrimary' => $image->is_primary,
                        'createdAt' => $image->created_at->toIso8601String(),
                    ];

                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Error al procesar la imagen: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        return response()->json([
            'message'  => 'Imágenes subidas correctamente',
            'uploaded' => count($uploadedImages),
            'images'   => $uploadedImages,
        ], 201);
    }

    /**
     * Obtener una imagen específica
     */
    public function show(Department $department, Image $image): JsonResponse
    {
        if (!$this->ensureOwnership($department, $image)) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        return response()->json([
            'id'           => $image->id,
            'departmentId' => $image->department_id,
            'fileName'     => $image->file_name,
            'url'          => url('/storage/' . $image->file_path),
            'fileSize'     => $image->file_size,
            'mimeType'     => $image->mime_type,
            'isPrimary'    => (bool) $image->is_primary,
            'uploadedBy'   => $image->uploaded_by,
            'createdAt'    => $image->created_at->toIso8601String(),
            'updatedAt'    => $image->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Actualizar información de la imagen
     */
    public function update(Request $request, Department $department, Image $image): JsonResponse
    {
        $this->authorize('update', $department);

        if (!$this->ensureOwnership($department, $image)) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate(['is_primary' => 'sometimes|boolean']);

        if ($request->has('is_primary')) {
            // Usamos el helper boolean de Laravel para mayor seguridad
            $isPrimary = $request->boolean('is_primary');

            if ($isPrimary) {
                // Optimización: Actualizar todas las demás a false excepto la actual
                $department->images()->where('id', '!=', $image->id)->update(['is_primary' => false]);
            }

            $image->update(['is_primary' => $isPrimary]);
        }

        return response()->json([
            'id'           => $image->id,
            'departmentId' => $image->department_id,
            'fileName'     => $image->file_name,
            'url'          => url('/storage/' . $image->file_path),
            'isPrimary'    => (bool) $image->is_primary,
            'updatedAt'    => $image->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Eliminar una imagen
     */
    public function destroy(Department $department, Image $image): JsonResponse
    {
        $this->authorize('update', $department);

        if (!$this->ensureOwnership($department, $image)) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        if (Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }

        $imageName = $image->file_name;
        $image->delete();

        return response()->json([
            'message' => 'Imagen eliminada correctamente',
            'deleted' => $imageName,
        ]);
    }

    /**
     * Obtener imagen primaria de un departamento
     */
    public function primary(Department $department): JsonResponse
    {
        $image = $department->images()->where('is_primary', true)->first();

        if (!$image) {
            return response()->json(['message' => 'No hay imagen primaria'], 404);
        }

        return response()->json([
            'id'       => $image->id,
            'fileName' => $image->file_name,
            'url'      => url('/storage/' . $image->file_path),
            'fileSize' => $image->file_size,
            'mimeType' => $image->mime_type,
        ]);
    }

    /**
     * Helper privado para verificar pertenencia sin repetir código
     */
    private function ensureOwnership(Department $department, Image $image): bool
    {
        return $image->department_id === $department->id;
    }
}

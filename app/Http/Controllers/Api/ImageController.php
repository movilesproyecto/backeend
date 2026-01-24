<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    /**
     * Obtener todas las imágenes de un departamento
     */
    public function index(Department $department)
    {
        $images = $department->images()
            ->select('id', 'department_id', 'file_name', 'file_path', 'file_size', 'mime_type', 'is_primary', 'created_at')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($image) {
                return [
                    'id' => $image->id,
                    'departmentId' => $image->department_id,
                    'fileName' => $image->file_name,
                    'url' => url('/storage/' . $image->file_path),
                    'fileSize' => $image->file_size,
                    'mimeType' => $image->mime_type,
                    'isPrimary' => $image->is_primary,
                    'createdAt' => $image->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'data' => $images,
            'total' => $images->count(),
        ]);
    }

    /**
     * Cargar una o múltiples imágenes
     */
    public function store(Request $request, Department $department)
    {
        // Autorizar que el usuario pueda actualizar el departamento
        $this->authorize('update', $department);

        // Validar archivos
        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB máximo
            'is_primary' => 'nullable|array',
            'is_primary.*' => 'boolean',
        ]);

        $user = Auth::user();
        $uploadedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                try {
                    // Generar nombre único
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Guardar en storage/app/public/departments/{id}/
                    $path = $file->storeAs(
                        'departments/' . $department->id,
                        $fileName,
                        'public'
                    );

                    // Si la imagen actual debe ser primaria
                    $isPrimary = isset($request->is_primary[$index]) && $request->is_primary[$index] === true;

                    // Si esta es la primera imagen, hacerla primaria por defecto
                    if ($index === 0 && !$department->images()->where('is_primary', true)->exists()) {
                        $isPrimary = true;
                    }

                    // Si esta debe ser primaria, las demás no lo serán
                    if ($isPrimary) {
                        $department->images()->update(['is_primary' => false]);
                    }

                    // Crear registro en base de datos
                    $image = Image::create([
                        'department_id' => $department->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => $user->id,
                        'is_primary' => $isPrimary,
                    ]);

                    $uploadedImages[] = [
                        'id' => $image->id,
                        'fileName' => $image->file_name,
                        'url' => url('/storage/' . $image->file_path),
                        'fileSize' => $image->file_size,
                        'mimeType' => $image->mime_type,
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
            'message' => 'Imágenes subidas correctamente',
            'uploaded' => count($uploadedImages),
            'images' => $uploadedImages,
        ], 201);
    }

    /**
     * Obtener una imagen específica
     */
    public function show(Department $department, Image $image)
    {
        // Verificar que la imagen pertenece al departamento
        if ($image->department_id !== $department->id) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        return response()->json([
            'id' => $image->id,
            'departmentId' => $image->department_id,
            'fileName' => $image->file_name,
            'url' => url('/storage/' . $image->file_path),
            'fileSize' => $image->file_size,
            'mimeType' => $image->mime_type,
            'isPrimary' => $image->is_primary,
            'uploadedBy' => $image->uploaded_by,
            'createdAt' => $image->created_at->toIso8601String(),
            'updatedAt' => $image->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Actualizar información de la imagen
     */
    public function update(Request $request, Department $department, Image $image)
    {
        // Autorizar
        $this->authorize('update', $department);

        // Verificar que la imagen pertenece al departamento
        if ($image->department_id !== $department->id) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        $request->validate([
            'is_primary' => 'sometimes|boolean',
        ]);

        if ($request->has('is_primary')) {
            if ($request->is_primary === true) {
                // Quitar primary de todas las otras imágenes
                $department->images()->update(['is_primary' => false]);
            }
            $image->is_primary = $request->is_primary;
            $image->save();
        }

        return response()->json([
            'id' => $image->id,
            'departmentId' => $image->department_id,
            'fileName' => $image->file_name,
            'url' => url('/storage/' . $image->file_path),
            'isPrimary' => $image->is_primary,
            'updatedAt' => $image->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Eliminar una imagen
     */
    public function destroy(Department $department, Image $image)
    {
        // Autorizar
        $this->authorize('update', $department);

        // Verificar que la imagen pertenece al departamento
        if ($image->department_id !== $department->id) {
            return response()->json(['message' => 'No encontrado'], 404);
        }

        // Eliminar archivo del storage
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
    public function primary(Department $department)
    {
        $image = $department->images()->where('is_primary', true)->first();

        if (!$image) {
            return response()->json(['message' => 'No hay imagen primaria'], 404);
        }

        return response()->json([
            'id' => $image->id,
            'fileName' => $image->file_name,
            'url' => url('/storage/' . $image->file_path),
            'fileSize' => $image->file_size,
            'mimeType' => $image->mime_type,
        ]);
    }
}

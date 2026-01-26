<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Review;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Importante para evitar errores visuales en el editor
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // Optimizamos la carga de imágenes usando una closure más limpia
        $query = Department::query()
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id')->limit(1)])
            ->where('published', true);

        // Filtros usando 'when' para mayor limpieza y legibilidad
        $query->when($request->input('q'), function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%")
                     ->orWhere('address', 'like', "%{$search}%");
            });
        });

        $query->when($request->input('min_price'), fn($q, $min) => $q->where('price_per_night', '>=', (float)$min));
        $query->when($request->input('max_price'), fn($q, $max) => $q->where('price_per_night', '<=', (float)$max));
        $query->when($request->input('bedrooms'), fn($q, $beds) => $q->where('bedrooms', '=', (int)$beds));

        // Sorting usando 'match' (PHP 8.0+)
        match ($request->input('sort')) {
            'price_asc'  => $query->orderBy('price_per_night', 'asc'),
            'price_desc' => $query->orderBy('price_per_night', 'desc'),
            'rating'     => $query->orderBy('rating_avg', 'desc'),
            default      => $query->orderByDesc('id'),
        };

        $perPage = (int) $request->input('per_page', 12);

        return DepartmentResource::collection($query->paginate($perPage)->appends($request->query()));
    }

    public function show(Department $department): JsonResponse
    {
        $department->load('images');

        // Mapeo limpio usando arrow function
        $images = $department->images->map(fn($image) => [
            'id'        => $image->id,
            'url'       => url('/storage/' . $image->file_path),
            'fileName'  => $image->file_name,
            'fileSize'  => $image->file_size,
            'mimeType'  => $image->mime_type,
            'isPrimary' => $image->is_primary,
        ])->toArray();

        return response()->json([
            'id'            => (string) $department->id,
            'name'          => $department->name,
            'address'       => $department->address ?? '',
            'bedrooms'      => $department->bedrooms ?? 1,
            'pricePerNight' => $department->price_per_night ?? $department->pricePerNight ?? 50,
            'rating'        => $department->rating ?? 4.0,
            'description'   => $department->description ?? '',
            'amenities'     => is_string($department->amenities) ? json_decode($department->amenities, true) : ($department->amenities ?? []),
            'images'        => $images,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->canCreateDepartment($user)) {
            return response()->json(['message' => 'No tienes permisos para crear departamentos'], 403);
        }

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'address'         => 'nullable|string',
            'bedrooms'        => 'nullable|integer|min:1',
            'price_per_night' => 'nullable|numeric|min:0',
            'pricePerNight'   => 'nullable|numeric|min:0', // Legacy support
            'amenities'       => 'nullable|array',
            'images'          => 'nullable|array',
        ]);

        // Unificamos la lógica del precio
        $priceValue = $data['price_per_night'] ?? $data['pricePerNight'] ?? 0;

        try {
            $dept = Department::create([
                'name'            => $data['name'],
                'description'     => $data['description'] ?? null,
                'address'         => $data['address'] ?? null,
                'bedrooms'        => $data['bedrooms'] ?? 1,
                'price_per_night' => $priceValue,
                'amenities'       => !empty($data['amenities']) ? json_encode($data['amenities']) : null,
                'images'          => !empty($data['images']) ? json_encode($data['images']) : json_encode([]),
                'published'       => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Departamento creado correctamente',
                'data'    => new DepartmentResource($dept)
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear departamento: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado. Por favor inicia sesión.', 'error' => 'UNAUTHENTICATED'], 401);
        }

        if (!$this->canUpdateDepartment($user)) {
            return response()->json(['message' => 'No tienes permisos para actualizar departamentos.', 'error' => 'UNAUTHORIZED'], 403);
        }

        try {
            $data = $request->validate([
                'name'            => 'sometimes|required|string|max:255',
                'description'     => 'nullable|string',
                'address'         => 'nullable|string',
                'bedrooms'        => 'nullable|integer|min:1',
                'price_per_night' => 'nullable|numeric|min:0',
                'pricePerNight'   => 'nullable|numeric|min:0',
                'rating'          => 'nullable|numeric',
                'amenities'       => 'nullable|array',
                'images'          => 'nullable|array',
            ]);

            $price = $request->input('price_per_night') ?? $request->input('pricePerNight') ?? $department->price_per_night;
            $updateData = [];

            // Lógica dinámica para verificar campos presentes en el request
            if ($request->has('name') && isset($data['name'])) $updateData['name'] = $data['name'];
            if ($request->has('description')) $updateData['description'] = $data['description'];
            if ($request->has('address')) $updateData['address'] = $data['address'];
            if ($request->has('bedrooms')) $updateData['bedrooms'] = $data['bedrooms'];

            // Caso especial precio (chequear ambas claves legacy/moderna)
            if ($request->has('price_per_night') || $request->has('pricePerNight')) {
                $updateData['price_per_night'] = $price;
            }

            if ($request->has('rating')) $updateData['rating_avg'] = $data['rating'];

            if ($request->has('amenities')) {
                $updateData['amenities'] = isset($data['amenities']) ? json_encode($data['amenities']) : null;
            }

            if ($request->has('images') && isset($data['images'])) {
                $updateData['images'] = json_encode($data['images']);
            }

            if (empty($updateData)) {
                return response()->json(['message' => 'No hay campos para actualizar.', 'error' => 'NO_UPDATE_FIELDS'], 400);
            }

            $department->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Departamento actualizado correctamente.',
                'data'    => new DepartmentResource($department->refresh())
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors(), 'error' => 'VALIDATION_ERROR'], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el departamento: ' . $e->getMessage(), 'error' => 'UPDATE_ERROR'], 500);
        }
    }

    public function favorite(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        DB::table('favorites')->updateOrInsert(
            ['user_id' => $user->id, 'department_id' => $department->id],
            ['updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => 'Favorited']);
    }

    public function unfavorite(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('department_id', $department->id)
            ->delete();

        return response()->json(['message' => 'Unfavorited']);
    }

    public function rate(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $data = $request->validate([
            'stars'   => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'user_id'       => $user->id,
            'department_id' => $department->id,
            'stars'         => $data['stars'],
            'comment'       => $data['comment'] ?? null,
        ]);

        // Optimización: Calcular promedio directamente en BD y redondear
        $avg = Review::where('department_id', $department->id)->avg('stars');
        $department->update(['rating_avg' => round($avg, 2)]);

        return response()->json($review, 201);
    }

    public function uploadImages(Request $request, Department $department): JsonResponse
    {
        $this->authorize('update', $department);
        $request->validate(['images.*' => 'image|max:5120']);

        $urls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('departments/' . $department->id, 'public');

                $department->images()->create([
                    'file_path'  => $path,
                    'file_name'  => $file->getClientOriginalName(),
                    'file_size'  => $file->getSize(),
                    'mime_type'  => $file->getMimeType(),
                    'is_primary' => $department->images()->doesntExist(), // Más legible que count === 0
                ]);

                $urls[] = Storage::url($path);
            }
        }

        return response()->json(['success' => true, 'images' => $urls]);
    }

    public function destroy(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);
        if (!$this->canDeleteDepartment($user)) return response()->json(['message' => 'No tienes permisos para eliminar departamentos'], 403);

        try {
            // Usar transacción para consistencia (opcional pero recomendado)
            DB::transaction(function () use ($department) {
                $department->images()->delete();
                $department->delete();
            });

            return response()->json(['success' => true, 'message' => 'Departamento eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar departamento: ' . $e->getMessage()], 500);
        }
    }

    public function uploadImageBinary(Request $request, Department $department): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!$this->canUpdateDepartment($user)) return response()->json(['message' => 'No autorizado'], 403);

        $request->validate(['image' => 'required|image|max:5120']);

        try {
            $file = $request->file('image');
            $base64 = base64_encode($file->get()); // 'get()' obtiene el contenido directamente

            $department->update(['images_binary' => $base64]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen guardada correctamente',
                'data'    => [
                    'id'           => $department->id,
                    'image_base64' => 'data:image/jpeg;base64,' . $base64
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al guardar imagen: ' . $e->getMessage()], 500);
        }
    }

    // --- Helpers Privados ---

    private function canCreateDepartment($user): bool
    {
        return $this->checkAdminRole($user);
    }

    private function canDeleteDepartment($user): bool
    {
        return $this->checkAdminRole($user);
    }

    private function canUpdateDepartment($user): bool
    {
        // En tu código original, Update permitía también a los correos "demo".
        // He unificado la lógica base pero mantengo la estructura por si la cambias luego.
        if (!$user) return false;

        if ($this->checkAdminRole($user)) return true;

        $admins = ['admin@demo.com', 'root@demo.com'];
        return in_array(strtolower($user->email), $admins);
    }

    private function checkAdminRole($user): bool
    {
        if (!$user) return false;
        $role = strtolower($user->role ?? 'user');
        return in_array($role, ['admin', 'superadmin', 'administrator']);
    }
}

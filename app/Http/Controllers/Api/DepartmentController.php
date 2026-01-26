<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;
use Illuminate\Support\Facades\Storage;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        // Solo cargar la primera imagen (portada) para optimizar la query
        $query = Department::query()
            ->with(['images' => function($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('id', 'asc')->limit(1);
            }])
            ->with('favoritedBy')
            ->where('published', true);

        // Filters
        if ($q = request('q')) {
            $query->where(function($qbu) use ($q){
                $qbu->where('name','like','%'.$q.'%')
                    ->orWhere('description','like','%'.$q.'%')
                    ->orWhere('address','like','%'.$q.'%');
            });
        }
        if ($min = request('min_price')) {
            $query->where('price_per_night','>=',(float)$min);
        }
        if ($max = request('max_price')) {
            $query->where('price_per_night','<=',(float)$max);
        }
        if ($bedrooms = request('bedrooms')) {
            $query->where('bedrooms','=',(int)$bedrooms);
        }

        // Sorting
        switch (request('sort')) {
            case 'price_asc': $query->orderBy('price_per_night','asc'); break;
            case 'price_desc': $query->orderBy('price_per_night','desc'); break;
            case 'rating': $query->orderBy('rating_avg','desc'); break;
            default: $query->orderBy('id','desc');
        }

        $perPage = (int) request('per_page', 12);
        $result = $query->paginate($perPage)->appends(request()->query());

        return DepartmentResource::collection($result)->response();
    }

    public function show(Department $department)
    {
        $department->load('images');
        $d = $department;

        // Mapear imágenes correctamente desde la tabla images
        $images = $d->images->map(function($image) {
            return [
                'id' => $image->id,
                'url' => url('/storage/' . $image->file_path),
                'fileName' => $image->file_name,
                'fileSize' => $image->file_size,
                'mimeType' => $image->mime_type,
                'isPrimary' => $image->is_primary,
            ];
        })->toArray();

        return response()->json([
            'id' => (string)$d->id,
            'name' => $d->name,
            'address' => $d->address ?? '',
            'bedrooms' => $d->bedrooms ?? 1,
            'pricePerNight' => $d->price_per_night ?? $d->pricePerNight ?? 50,
            'rating' => $d->rating ?? 4.0,
            'description' => $d->description ?? '',
            'amenities' => is_string($d->amenities) ? json_decode($d->amenities, true) : ($d->amenities ?? []),
            'images' => $images,
        ]);
    }

    public function store(Request $request)
    {
        // Verificar autenticación
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos
        if (!$this->canCreateDepartment($user)) {
            return response()->json(['message' => 'No tienes permisos para crear departamentos'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:1',
            'price_per_night' => 'nullable|numeric|min:0',
            'pricePerNight' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        // Accept both pricePerNight and price_per_night
        $priceValue = $request->input('price_per_night') ?? $request->input('pricePerNight') ?? 0;

        try {
            $dept = Department::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'bedrooms' => $data['bedrooms'] ?? 1,
                'price_per_night' => $priceValue,
                'amenities' => $data['amenities'] ? json_encode($data['amenities']) : null,
                'images' => $data['images'] ? json_encode($data['images']) : json_encode([]),
                'published' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Departamento creado correctamente',
                'data' => new DepartmentResource($dept)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear departamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Department $department)
    {
        // Verificar autenticación primero
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'No autenticado. Por favor inicia sesión.',
                'error' => 'UNAUTHENTICATED'
            ], 401);
        }

        // Verificar autorización
        if (!$this->canUpdateDepartment($user)) {
            return response()->json([
                'message' => 'No tienes permisos para actualizar departamentos.',
                'error' => 'UNAUTHORIZED'
            ], 403);
        }

        try {
            // Accept both pricePerNight and price_per_night
            $priceValue = $request->input('price_per_night') ?? $request->input('pricePerNight');

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'bedrooms' => 'nullable|integer|min:1',
                'price_per_night' => 'nullable|numeric|min:0',
                'pricePerNight' => 'nullable|numeric|min:0',
                'rating' => 'nullable|numeric',
                'amenities' => 'nullable|array',
                'images' => 'nullable|array',
            ]);

            // Use the price value from either key
            $price = $priceValue ?? $department->price_per_night;

            $updateData = [];

            // Solo actualizar campos que fueron enviados
            if ($request->has('name') && isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if ($request->has('description')) {
                $updateData['description'] = $data['description'];
            }
            if ($request->has('address')) {
                $updateData['address'] = $data['address'];
            }
            if ($request->has('bedrooms')) {
                $updateData['bedrooms'] = $data['bedrooms'];
            }
            if ($request->has('price_per_night') || $request->has('pricePerNight')) {
                $updateData['price_per_night'] = $price;
            }
            if ($request->has('rating')) {
                $updateData['rating_avg'] = $data['rating'];
            }
            if ($request->has('amenities')) {
                $updateData['amenities'] = isset($data['amenities']) ? json_encode($data['amenities']) : null;
            }
            if ($request->has('images') && isset($data['images'])) {
                // Guardar las URLs de imágenes directamente como JSON
                $updateData['images'] = json_encode($data['images']);
            }

            // Si no hay datos para actualizar, devolver error
            if (empty($updateData)) {
                return response()->json([
                    'message' => 'No hay campos para actualizar.',
                    'error' => 'NO_UPDATE_FIELDS'
                ], 400);
            }

            $department->update($updateData);
            $department->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Departamento actualizado correctamente.',
                'data' => new DepartmentResource($department)
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'error' => 'VALIDATION_ERROR'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el departamento: ' . $e->getMessage(),
                'error' => 'UPDATE_ERROR'
            ], 500);
        }
    }

    private function canUpdateDepartment($user)
    {
        if (!$user) return false;

        // Check role
        $role = $user->role ?? null;
        if ($role && in_array(strtolower($role), ['admin', 'superadmin', 'administrator'])) {
            return true;
        }

        // Fallback: allow known demo admin emails
        $admins = [
            'admin@demo.com',
            'root@demo.com',
        ];

        if (in_array(strtolower($user->email), $admins)) {
            return true;
        }

        return false;
    }

    // Favorite endpoint
    public function favorite(Request $request, Department $department)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message'=>'Unauthorized'],401);
        // create favorite if not exists
        \DB::table('favorites')->updateOrInsert([
            'user_id' => $user->id,
            'department_id' => $department->id,
        ], ['updated_at' => now(), 'created_at' => now()]);
        return response()->json(['message'=>'Favorited']);
    }

    public function unfavorite(Request $request, Department $department)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message'=>'Unauthorized'],401);
        \DB::table('favorites')->where('user_id',$user->id)->where('department_id',$department->id)->delete();
        return response()->json(['message'=>'Unfavorited']);
    }

    // Rate / review endpoint
    public function rate(Request $request, Department $department)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message'=>'Unauthorized'],401);
        $data = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $review = Review::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'stars' => $data['stars'],
            'comment' => $data['comment'] ?? null,
        ]);
        // update avg rating
        $avg = Review::where('department_id',$department->id)->avg('stars');
        $department->rating_avg = round($avg,2);
        $department->save();
        return response()->json($review,201);
    }

    // Upload images
    public function uploadImages(Request $request, Department $department)
    {
        $this->authorize('update', $department);
        $request->validate(['images.*' => 'image|max:5120']);

        $urls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Guardar archivo en storage
                $path = $file->store('departments/'.$department->id, 'public');
                $url = Storage::url($path);

                // Crear registro en tabla images
                $department->images()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'is_primary' => $department->images()->count() === 0, // Primera imagen es principal
                ]);

                $urls[] = $url;
            }
        }

        return response()->json([
            'success' => true,
            'images' => $urls
        ]);
    }

    public function destroy(Department $department)
    {
        // Verificar autenticación
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar permisos
        if (!$this->canDeleteDepartment($user)) {
            return response()->json(['message' => 'No tienes permisos para eliminar departamentos'], 403);
        }

        try {
            // Eliminar imágenes asociadas
            $department->images()->delete();

            // Eliminar el departamento
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Departamento eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar departamento: ' . $e->getMessage()
            ], 500);
        }
    }

    // Subir imagen como binario (bytea en PostgreSQL)
    public function uploadImageBinary(Request $request, Department $department)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!$this->canUpdateDepartment($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'image' => 'required|image|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('image');
            $imageBinary = file_get_contents($file->getRealPath());

            // Convertir a base64 para almacenar sin problemas de encoding
            $base64 = base64_encode($imageBinary);

            // Guardar como base64 en BD (text o varchar)
            $department->update([
                'images_binary' => $base64
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen guardada correctamente',
                'data' => [
                    'id' => $department->id,
                    'image_base64' => 'data:image/jpeg;base64,' . $base64
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos auxiliares para verificar permisos
    private function canCreateDepartment($user)
    {
        if (!$user) return false;
        $role = strtolower($user->role ?? 'user');
        return in_array($role, ['admin', 'superadmin']);
    }

    private function canDeleteDepartment($user)
    {
        if (!$user) return false;
        $role = strtolower($user->role ?? 'user');
        return in_array($role, ['admin', 'superadmin']);
    }
}

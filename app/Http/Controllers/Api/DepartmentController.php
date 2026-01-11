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
        $query = Department::query()->where('published', true);

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
        $d = $department;
        return response()->json([
            'id' => (string)$d->id,
            'name' => $d->name,
            'address' => $d->address ?? '',
            'bedrooms' => $d->bedrooms ?? 1,
            'pricePerNight' => $d->price_per_night ?? $d->pricePerNight ?? 50,
            'rating' => $d->rating ?? 4.0,
            'description' => $d->description ?? '',
            'amenities' => $d->amenities ?? [],
            'images' => $d->images ?? [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'pricePerNight' => 'nullable|numeric',
            'rating' => 'nullable|numeric',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $dept = Department::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? 1,
            'price_per_night' => $data['pricePerNight'] ?? 0,
            'rating_avg' => $data['rating'] ?? 0,
            'amenities' => $data['amenities'] ? json_encode($data['amenities']) : null,
            'images' => $data['images'] ? json_encode($data['images']) : null,
        ]);

        return new DepartmentResource($dept);
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'pricePerNight' => 'nullable|numeric',
            'rating' => 'nullable|numeric',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $department->update([
            'name' => $data['name'] ?? $department->name,
            'description' => $data['description'] ?? $department->description,
            'address' => $data['address'] ?? $department->address,
            'bedrooms' => $data['bedrooms'] ?? $department->bedrooms,
            'price_per_night' => $data['pricePerNight'] ?? $department->price_per_night,
            'rating_avg' => $data['rating'] ?? $department->rating_avg,
            'amenities' => isset($data['amenities']) ? json_encode($data['amenities']) : $department->amenities,
            'images' => isset($data['images']) ? json_encode($data['images']) : $department->images,
        ]);

        return new DepartmentResource($department);
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
                $path = $file->store('departments/'.$department->id, 'public');
                $urls[] = Storage::url($path);
            }
            // merge into department images
            $existing = $department->images ? json_decode($department->images, true) : [];
            $department->images = json_encode(array_merge($existing, $urls));
            $department->save();
        }
        return response()->json(['images' => $urls]);
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);
        $department->delete();
        return response()->json(null, 204);
    }
}

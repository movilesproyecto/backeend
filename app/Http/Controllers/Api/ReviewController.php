<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // List reviews for a department (public)
    public function index(Request $request, $departmentId)
    {
        $perPage = max(1, (int) $request->query('per_page', 20));
        $reviews = Review::where('department_id', $departmentId)->with('user')->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($reviews);
    }

    // Create a review for a department (authenticated)
    public function store(Request $request, $departmentId)
    {
        $dept = Department::find($departmentId);
        if (!$dept) {
            return response()->json(['message' => 'Departamento no encontrado.'], 404);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();

        $review = Review::create([
            'department_id' => $departmentId,
            'user_id' => $user ? $user->id : null,
            'stars' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        // Optionally, recalculate department rating (simple avg)
        try {
            $avg = Review::where('department_id', $departmentId)->avg('stars');
            $dept->rating = round($avg, 2);
            $dept->save();
        } catch (\Throwable $e) {
            // ignore
        }

        return response()->json(['success' => true, 'review' => $review], 201);
    }
}

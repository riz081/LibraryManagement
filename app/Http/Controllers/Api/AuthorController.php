<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorController extends Controller
{
    public function index()
    {
        // Retrieve all authors with their books
        $authors = Cache::remember('authors', 60, function () {
            return Author::select('id', 'name', 'bio', 'birth_date')
                ->with('books:id,title,author_id')
                ->get();
        });

        return response()->json([
            'status' => 200,
            'message' => 'Authors retrieved successfully',
            'data' => $authors
        ], 200);
    }

    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'birth_date' => 'required|date',
        ]);

        // Create a new author
        $author = Author::create($request->all());

        return response()->json([
            'status' => 201,
            'message' => 'Author created successfully',
            'data' => $author
        ], 201); // Return 201 status for resource creation
    }

    public function show($id)
    {
        $author = Cache::remember("author_{$id}", 60, function () use ($id) {
            return Author::with('books:id,title,author_id')->findOrFail($id); // Eager load with only necessary columns
        });

        return response()->json([
            'status' => 200,
            'message' => 'Author retrieved successfully',
            'data' => $author
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request data
            $request->validate([
                'name' => 'required|string|max:255',
                'bio' => 'nullable|string',
                'birth_date' => 'required|date',
            ]);

            // Find author and update their data
            $author = Author::findOrFail($id);
            $author->update($request->all());

            // Clear the cache for the updated author
            Cache::forget("author_{$id}");

            return response()->json([
                'status' => 200,
                'message' => 'Author updated successfully',
                'data' => $author
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found',
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            // Find author by ID
            $author = Author::findOrFail($id);

            // Delete the author and cascade delete their books
            $author->delete();

            // Clear the cache for the deleted author
            Cache::forget("author_{$id}");

            return response()->json([
                'status' => 200,
                'message' => 'Author deleted successfully',
                'data' => null
            ], 200); // Return 200 status
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found',
            ], 404);
        }
    }

    public function getBooksByAuthor($id)
    {
        try {
            // Find the author and get all associated books
            $author = Author::with('books:id,title,author_id')->findOrFail($id);

            return response()->json([
                'status' => 200,
                'message' => 'Books retrieved successfully for the specified author',
                'data' => $author->books
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Author not found',
            ], 404);
        }
    }
}

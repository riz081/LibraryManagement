<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{    
    public function index()
    {
        $books = Cache::remember('books', 60, function () {
            return Book::select('id', 'title', 'description', 'publish_date', 'author_id')
                ->with('author:id,name')
                ->get();
        });

        return response()->json([
            'status' => 200,
            'message' => 'Books retrieved successfully',
            'data' => $books
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publish_date' => 'required|date',
            'author_id' => 'required|exists:authors,id',
        ]);

        $book = Book::create($request->all());

        Cache::forget('books');

        return response()->json([
            'status' => 201,
            'message' => 'Book created successfully',
            'data' => $book
        ], 201); 
    }

    public function show($id)
    {
        $book = Cache::remember("book_{$id}", 60, function () use ($id) {
            return Book::with('author:id,name')->findOrFail($id); // Eager load with only necessary columns
        });

        return response()->json([
            'status' => 200,
            'message' => 'Book retrieved successfully',
            'data' => $book
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publish_date' => 'required|date',
            'author_id' => 'required|exists:authors,id',
        ]);

        $book = Book::findOrFail($id);
        $book->update($request->all());

        Cache::forget("book_{$id}");
        Cache::forget('books');

        return response()->json([
            'status' => 200,
            'message' => 'Book updated successfully',
            'data' => $book
        ], 200);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $book->delete();
        Cache::forget('books');
        Cache::forget("book_{$id}"); 

        return response()->json([
            'status' => 204,
            'message' => 'Book deleted successfully',
            'data' => null
        ], 200); 
    }
}

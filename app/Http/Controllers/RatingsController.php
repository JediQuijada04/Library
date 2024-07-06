<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RatingsController extends Controller
{
    public function addRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $cacheKey = 'books_' . $id;
        $book = Book::findOrFail($id);
        if ($book) {
            Cache::forget('books_' . $book->title);
            $rating = $book->ratings()->create([
                'product_id' => $book->id,
                'rating' => $request->rating,
            ]);

            return response()->json($rating, 201);
        } else {
            return response()->json(['result' => 'Book not found'], 404);
        }
    }

}



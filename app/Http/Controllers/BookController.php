<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\OrderBook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{  public function getBook(Book $book)
    {

        if (!$book->exists) {

            return response()->json(['error' => 'Book not found'], 404);
        }


        $cacheKey = 'books_' . $book->id;
        $bookDetails = Cache::remember($cacheKey, 3600, function () use ($book) {

            return $book;
        });

        return response()->json($bookDetails);
    }

    public function getBooks()
    {
        $cacheKey = 'books_';
        $books = Cache::remember($cacheKey, 3600, function () {
            return Book::with('ratings')->get();
        });

        return response()->json($books);
    }
    public function trashedBooks()
    {
        $cacheKey = 'trashed_books';
        $books = Cache::remember($cacheKey, 3600, function () {
            return Book::onlyTrashed()->with('ratings')->get(); ; // Fetch the results and then cache
        });

        return response()->json($books);
    }



    public function addBook(Request $request) {
        try {
            // Validate request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'book_file_path' => 'required|file|mimes:pdf|max:10240', // Max file size 10MB
                'status' => 'required|string', // Ensure 'status' is required
            ]);

            // Handle file upload
            if ($request->hasFile('book_file_path')) {
                $uploadedFile = $request->file('book_file_path');
                $filename = $uploadedFile->getClientOriginalName();
                $uploadedFilePath = $uploadedFile->storeAs('public/uploads', $filename);

                // Create new book entry
                $book = new Book();
                $book->title = $request->title;
                $book->description = $request->description;
                $book->book_file_path = $uploadedFilePath;
                $book->status = $request->status;
                $book->section_id = $request->section_id;

                // Save the book and return response
                if ($book->save()) {
                    Cache::forget('books_' . $book->title);
                    return response()->json(['result' => 'Book added successfully']);
                } else {
                    Log::error('Failed to save the book to the database', ['book' => $book]);
                    return response()->json(['result' => 'Failed to add book'], 500);
                }
            } else {
                return response()->json(['result' => 'File upload failed'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while adding book', ['error' => $e->getMessage()]);
            return response()->json(['result' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

     public function deleteBook($id){
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }


        $book->delete();
        Cache::forget('books_' . $book->title);
        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

     public function forceDeleteBook($id){
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

       // Delete the associated file from the storage
        if (Storage::exists($book->book_file_path)) {
            Storage::delete($book->book_file_path);
        }


        $book->forceDelete();
        Cache::forget('books_' . $book->title);
        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

     public function restoreBook($id){
        $book = Book::withTrashed()->find($id);

        if(!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->restore();
        Cache::forget('books_' . $book->title);
        return response()->json(['message' => 'Book restored successfully'], 200);
    }

     public function editBook(Request $request, $id) {

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);


        $book = Book::find($id);
        if ($book) {

        $book->title = $request->title;
        $book->description = $request->description;
        $book->update();
        Cache::forget('books_' . $book->title);
        return response()->json(['result' => 'Book updated successfully']);

        }
         else {
            return response()->json(['result' => 'Failed to update book'], 500);
        }
    }
    public function orderBook(Request $request)
    {
        $book_id = $request->query('book_id');
        $book = Book::find($book_id);

        if ($book == null) {
            return response()->json(['result' => 'Book not found'], 404);
        }

        if ($book->status == 'Available') {


            $order = new OrderBook();
            $order->book_id = $book->id;
            $order->user_id = $request->user()->id;
            $order->save();

            return response()->json(['result' => 'Book ordered successfully']);
        } else {
            return response()->json(['result' => 'Book not available'], 404);
        }
    }

}




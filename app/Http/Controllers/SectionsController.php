<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sections;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
class SectionsController extends Controller
{
    public function addSection(Request $request) {
        try {
            // Validate request
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',

            ]);

                // Create new book entry
                $section = new Sections();
                $section->name = $request->name;
                $section->description = $request->description;
                $section->save();

                // Save the book and return response
                if ($section->save()) {
                    Cache::forget('sections_' . $section->title);
                    return response()->json(['result' => 'Section added successfully']);
                } else {
                    Log::error('Failed to save the section to the database', ['section' => $section]);
                    return response()->json(['result' => 'Failed to add section'], 500);
                }
            }
         catch (\Exception $e) {
            Log::error('Exception occurred while adding section', ['error' => $e->getMessage()]);
            return response()->json(['result' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function countBooks($sectionId)
    {
        $section = Sections::findOrFail($sectionId);
        $bookCount = $section->books()->count();

        return response()->json(['book_count' => $bookCount]);
    }

}

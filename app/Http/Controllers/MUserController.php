<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\mUser;

class MUserController extends Controller
{
    public function addDataEntry(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:100|unique:m_users',
            'password' =>  'required|string|min:6'
        ]);

        $newDataEntry = new mUser();
        $newDataEntry->name = $request->name;
        $newDataEntry->role_id = 2;
        $newDataEntry->email = $request->email;

        // Hash the password before saving
        $newDataEntry->password = Hash::make($request->password);

        $results = $newDataEntry->save();
        if ($results) {
            return response()->json(['result' => 'DataEntry added successfully']);
        } else {
            return response()->json(['result' => 'Failed to add DataEntry']);
        }
    }
}

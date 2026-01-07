<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompleteCredentialController extends Controller
{

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'studentID' => 'required',
            'department' => 'required',
            'gender' => 'required|in:Male,Female',
            'password' => 'required',
        ]);

        $user->studentID = $request->studentID;
        $user->department = $request->department;
        $user->gender = $request->gender;
        $user->password = $request->password;
        $user->save();

        return redirect()->route('index')->with('success', 'Profile updated successfully!');
    }

}
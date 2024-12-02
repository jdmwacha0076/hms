<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    // For viewing all the users
    public function viewAllUsers()
    {
        $users = User::where('id', '>=', 2)
            ->orderBy('id', 'asc')
            ->get();
        $loggedInUserId = Auth::id();
        return view('users.user-management', compact('users', 'loggedInUserId'));
    }

    // For updating the user name and email
    public function updateUser(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kubadili taarifa za mtumiaji.');
        }

        $request->validate([
            'id' => 'required|exists:users,id',
            'email' => 'required|string|email|max:255',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('success', 'Umefanikiwa kubadili jina la mtumiaji.');
    }

    // For adding a new user
    public function addUser(Request $request)
    {
        if (Auth::user()->user_role != 1) {
            return redirect()->back()->with('error', 'Huna ruhusa ya kuongeza mtumiaji.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:15|regex:/^(?:255)[0-9]{9}$/',
        ]);

        if ($validator->fails()) {
            $messages = [];

            if ($validator->errors()->has('email')) {
                $messages['email'] = 'Barua pepe hii tayari imeshasajiliwa.';
            }

            if ($validator->errors()->has('password')) {
                $messages['password'] = 'Neno siri lazima liwe na herufi nane au zaidi.';
            }
            return redirect()->back()->withErrors($messages)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_role' => $request['user_role'],
            'phone_number' => $request['phone_number']
        ]);

        return redirect()->back()->with('success', 'Umefanikiwa kumuongeza mtumiaji.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (auth::check()) {
            return redirect()->intended('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request data
        $creds = $request->validate([
            'name' => 'required|max:30',
            'password' => 'required|min:5',
        ]);

        // Attempt to log the user in
        if (Auth::attempt($creds, $request->filled('remember'))) {
            // If successful, redirect to the intended page
            return redirect()->intended('dashboard');
        }

        // If login fails, redirect back with an error message
        return redirect()->back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        // Log the user out
        Auth::logout();

        // Redirect to the login page
        return redirect()->route('login');
    }
}

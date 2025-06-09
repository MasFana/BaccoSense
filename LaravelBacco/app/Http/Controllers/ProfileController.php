<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'current_password' => 'nullable|string|min:5',
            'new_password' => 'nullable|string|min:5',
        ]);

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Password saat ini salah');
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui');
    }
}

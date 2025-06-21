<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthViewController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'profile_photo' => 'nullable|image|max:2048',
            'foto' => 'nullable|image|max:2048',
        ]);

        $profilePhotoName = null;
        $fotoName = null;

        if ($request->hasFile('profile_photo')) {
            $profilePhotoName = uniqid() . '.' . $request->file('profile_photo')->getClientOriginalExtension();
            $request->file('profile_photo')->storeAs('public/uploads/profile_photos', $profilePhotoName);
        }

        if ($request->hasFile('foto')) {
            $fotoName = uniqid() . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/foto', $fotoName);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_photo' => $profilePhotoName,
            'foto' => $fotoName,
            'role' => 'peserta',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}

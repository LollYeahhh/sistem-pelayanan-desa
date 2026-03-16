<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        if (Auth::attempt($credentials, $request->boolean("remember"))) {
            $request->session()->regenerate();
            return match (auth()->user()->role) {
                "admin" => redirect()->route("admin.dashboard"),
                "rt" => redirect()->route("rt.dashboard"),
                "warga" => redirect()->route("warga.dashboard"),
            };
        }
        return back()
            ->withErrors([
                "email" => "Email atau password salah.",
            ])
            ->onlyInput("email");
    }
    public function showRegister()
    {
        return view("auth.register");
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            "nama_lengkap" => "required|string|max:255",
            "nik" => "required|string|size:16|unique:users",
            "email" => "required|email|unique:users",
            "no_hp" => "required|string|max:20",
        ]);

        $validated["password"] = Hash::make($validated["password"]);
        $validated["role"] = "warga";

        User::create($validated);

        return redirect()
            ->route("login")
            ->with("success", "Registrasi berhasil. Silakan login.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}

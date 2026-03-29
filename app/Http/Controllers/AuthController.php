<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminNewUserActivationNotification;
use Illuminate\Http\Request;
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
            "email" => "required|email|unique:users",
            "no_hp" => "required|string|max:20",
            "password" => "required|string|min:8|confirmed",
        ]);

        $validated["role"] = "warga";
        $validated["nik"] = null;
        $validated["is_verified"] = false;
        $validated["status_akun"] = "nonaktif";

        $user = User::create($validated);

        $this->dispatchAdminNotification(
            new AdminNewUserActivationNotification($user),
        );

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

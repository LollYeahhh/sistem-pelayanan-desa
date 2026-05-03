<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerifiedAndActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route("login");
        }

        if (! $user->is_verified || $user->status_akun !== "aktif") {
            $dashboardRoute = match ($user->role) {
                "rt" => "rt.dashboard",
                "warga" => "warga.dashboard",
                default => "home",
            };
            $message = ! $user->is_verified
                ? "Akun Anda masih menunggu verifikasi admin. Fitur ajukan layanan akan tersedia setelah akun diaktifkan."
                : "Akun Anda sedang nonaktif. Hubungi admin desa untuk mengaktifkan kembali akses ajukan layanan.";

            return redirect()
                ->route($dashboardRoute)
                ->with("error", $message);
        }

        return $next($request);
    }
}

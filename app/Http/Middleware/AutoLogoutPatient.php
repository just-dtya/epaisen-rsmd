<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutPatient
{
    /**
     * Waktu idle maksimum dalam detik (contoh: 15 menit = 900 detik)
     */
    protected $timeout = 900;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');

            // Jika ada catatan aktivitas terakhir dan melebihi batas waktu
            if ($lastActivity && (time() - $lastActivity > $this->timeout)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'login_error' => 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.'
                ]);
            }

            // Perbarui waktu aktivitas terakhir
            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}

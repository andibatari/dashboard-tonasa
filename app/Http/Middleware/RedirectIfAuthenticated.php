<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // --- KOREKSI PENTING UNTUK API ---
                // Jika pengguna sudah login dan mencoba mengakses rute non-API, 
                // ini akan mengalihkan mereka ke RouteServiceProvider::HOME (misal: /home).
                
                // Jika rute ini diakses oleh request API (misalnya, jika API login dipanggil 
                // padahal user sudah punya token yang valid), kita harus mengembalikan JSON.
                
                if ($request->expectsJson() || $request->is('api/*')) {
                    // Mengembalikan respons JSON jika pengguna sudah terautentikasi 
                    // dan request datang dari API.
                    return response()->json(['message' => 'Already authenticated.'], 200);
                }

                // Perilaku default untuk aplikasi Web: redirect ke /home
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
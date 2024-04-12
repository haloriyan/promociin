<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if ($user == null) {
            $currentURL = URL::current();

            return redirect()->route('admin.loginPage', ['r' => base64_encode($currentURL)])->withErrors([
                'You must be logged in before accessing this page'
            ]);
        }
        return $next($request);
    }
}

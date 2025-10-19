<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
  public function handle($request, Closure $next)
{
    $user = auth()->user();
    if (!$user) {
        abort(403);
    }

    // Autoriser le rôle admin OU un utilisateur précis (id=4)
    if ($user->role === 'admin' || $user->id === 4) {
        return $next($request);
    }

    abort(403); // sinon bloque
}

}
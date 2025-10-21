<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ Authentifie l'utilisateur
        $request->authenticate();

        // ✅ Regénère la session pour sécurité
        $request->session()->regenerate();

        // ✅ Récupère l'utilisateur connecté
        $user = Auth::user();

        // ✅ Redirige selon le rôle
        if ($user->role === 'admin') {
            // Redirection vers le tableau de bord administrateur
            return redirect()->route('admin.challenges.index');
        }

        // Redirection vers le tableau de bord utilisateur
        return redirect()->route('challenges.index');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Déconnexion de l'utilisateur
        Auth::guard('web')->logout();

        // ✅ Invalidation de la session
        $request->session()->invalidate();

        // ✅ Régénération du token CSRF
        $request->session()->regenerateToken();

        // ✅ Redirection vers la page d'accueil
        return redirect('/');
    }
}

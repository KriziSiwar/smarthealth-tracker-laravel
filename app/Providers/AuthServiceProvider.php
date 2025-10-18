<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les policies de l'application.
     */
    protected $policies = [
        // 'App\Models\Activity' => 'App\Policies\ActivityPolicy',
    ];

    /**
     * Enregistrement des services d'autorisation.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ Autorise toutes les actions pour tous les utilisateurs
        Gate::before(function ($user, $ability) {
            return true;
        });
    }
}

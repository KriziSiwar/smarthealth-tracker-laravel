<?php

namespace App\Policies;

use App\Models\MealPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir n'importe quel modèle.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, MealPlan $mealPlan): bool
    {
        // L'utilisateur peut voir son propre plan ou un plan public
        return $user->id === $mealPlan->user_id || $mealPlan->is_public;
    }

    /**
     * Détermine si l'utilisateur peut créer des modèles.
     */
    public function create(User $user): bool
    {
        // Tout utilisateur authentifié peut créer un plan de repas
        return true;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, MealPlan $mealPlan): bool
    {
        // Seul le propriétaire peut mettre à jour le plan
        return $user->id === $mealPlan->user_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, MealPlan $mealPlan): bool
    {
        // Seul le propriétaire peut supprimer le plan
        return $user->id === $mealPlan->user_id;
    }

    /**
     * Détermine si l'utilisateur peut restaurer le modèle.
     */
    public function restore(User $user, MealPlan $mealPlan): bool
    {
        return $user->id === $mealPlan->user_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement le modèle.
     */
    public function forceDelete(User $user, MealPlan $mealPlan): bool
    {
        return $user->id === $mealPlan->user_id;
    }
}

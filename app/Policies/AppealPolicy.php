<?php

namespace App\Policies;

use App\Models\Appeal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppealPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Qualquer usuário autenticado pode ver a lista de seus recursos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appeal $appeal): bool
    {
        return $user->id === $appeal->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Qualquer usuário autenticado pode criar recursos
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appeal $appeal): bool
    {
        return $user->id === $appeal->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appeal $appeal): bool
    {
        return $user->id === $appeal->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appeal $appeal): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appeal $appeal): bool
    {
        return $user->is_admin;
    }
}

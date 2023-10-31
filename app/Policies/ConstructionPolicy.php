<?php

namespace App\Policies;

use App\Enums\Permissions\Constructions;
use App\Models\Construction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConstructionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->havePermissionTo((Constructions::VIEW)->code());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->havePermissionTo((Constructions::CREATE)->code());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Construction $construction): bool
    {
        return $user->havePermissionTo((Constructions::UPDATE)->code());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Construction $construction): bool
    {
        return $user->havePermissionTo((Constructions::DELETE)->code());
    }
}

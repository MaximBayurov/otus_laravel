<?php

namespace App\Policies;

use App\Enums\Permissions\Languages;
use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LanguagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->havePermissionTo((Languages::VIEW)->code());
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->havePermissionTo((Languages::CREATE)->code());
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Language $language): bool
    {
        return $user->havePermissionTo((Languages::UPDATE)->code());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Language $language): bool
    {
        return $user->havePermissionTo((Languages::DELETE)->code());
    }
}

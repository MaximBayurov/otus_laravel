<?php

namespace App\Policies;

use App\Models\User;
use App\Services\ImportService;
use Illuminate\Support\Facades\Auth;

class AdminImportPolicy
{
    public function __construct(
        protected ImportService $importService
    ) {
    }

    public function index(User $user): bool
    {
        return !empty($this->importService->getAllowedModels());
    }

    /**
     * Может ли текущий пользователь импортировать модель
     *
     * @param \App\Models\User $user
     * @param string $modelClass
     *
     * @return bool
     */
    public function canImport(User $user, string $modelClass): bool
    {
        return Auth::user()->can('create', $modelClass);
    }
}

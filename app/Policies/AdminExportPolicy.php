<?php

namespace App\Policies;

use App\Models\User;
use App\Services\ExportService;
use Illuminate\Support\Facades\Auth;

class AdminExportPolicy
{

    public function __construct(
        protected ExportService $exportService
    ) {
    }

    public function index(User $user): bool
    {
        return !empty($this->exportService->getAllowedModels());
    }

    /**
     * Может ли текущий пользователь экспортировать модель
     *
     * @param \App\Models\User $user
     * @param string $modelClass
     *
     * @return bool
     */
    public function canExport(User $user, string $modelClass): bool
    {
        return $user->can('viewAny', $modelClass);
    }
}

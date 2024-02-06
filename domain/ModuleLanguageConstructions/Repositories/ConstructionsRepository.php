<?php

namespace Domain\ModuleLanguageConstructions\Repositories;

use Domain\ModuleLanguageConstructions\Models\Construction;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория сущности "Языковые конструкции"
 */
interface ConstructionsRepository
{
    public function getOptions(): array;

    public function getPagination(int $page): LengthAwarePaginator;

    public function add(array $construction): Construction;

    public function update(Construction $construction, array $fields): void;

    public function delete(Construction $construction): void;
}

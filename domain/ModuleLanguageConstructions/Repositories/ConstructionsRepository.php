<?php

namespace Domain\ModuleLanguageConstructions\Repositories;

use App\Enums\PageSizesEnum;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория сущности "Языковые конструкции"
 */
interface ConstructionsRepository
{
    public function getOptions(): array;

    public function getPagination(int $page, PageSizesEnum $pageSize): LengthAwarePaginator;

    public function add(array $construction): Construction;

    public function update(Construction $construction, array $fields): void;

    public function delete(Construction $construction): void;

    public function getAll(): Collection;

    public function getBySlug(string $slug): ?Construction;
}

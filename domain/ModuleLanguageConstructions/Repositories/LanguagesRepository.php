<?php

namespace Domain\ModuleLanguageConstructions\Repositories;

use App\Enums\PageSizesEnum;
use Domain\ModuleLanguageConstructions\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория сущности "Языки программирования"
 */
interface LanguagesRepository
{
    public function getOptions(): array;

    public function getPagination(int $page, PageSizesEnum $pageSize): LengthAwarePaginator;

    public function add(array $language): ?Language;

    public function update(Language $language, array $fields): bool;

    public function delete(Language $language): bool;

    public function getBySlug(string $slug): ?Language;

    public function getAll(): Collection;
}

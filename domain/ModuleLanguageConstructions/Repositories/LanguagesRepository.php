<?php

namespace Domain\ModuleLanguageConstructions\Repositories;

use Domain\ModuleLanguageConstructions\Models\Language;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория сущности "Языки программирования"
 */
interface LanguagesRepository
{
    public function getOptions(): array;

    public function getPagination(int $page): LengthAwarePaginator;

    public function add(array $language): Language;

    public function update(Language $language, array $fields): void;

    public function delete(Language $language): void;
}

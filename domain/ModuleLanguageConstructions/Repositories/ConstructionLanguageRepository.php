<?php

namespace Domain\ModuleLanguageConstructions\Repositories;

use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;

/**
 * Интерфейс репозитория сущности "Реализации языковых конструкций"
 */
interface ConstructionLanguageRepository
{

    public function collectLanguagesFormattedFor(Construction $construction): array;

    public function collectConstructionsFormattedFor(Language $language): array;

    public function addForConstruction(Construction $construction, array $languages): void;

    public function updateForConstruction(Construction $construction, array $languages): void;

    public function addForLanguage(Language $language, array $constructions): void;

    public function updateForLanguage(Language $language, array $constructions): void;

}

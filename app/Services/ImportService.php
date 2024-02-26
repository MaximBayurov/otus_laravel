<?php

namespace App\Services;

use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;
use Illuminate\Support\Facades\Auth;

class ImportService
{
    const IMPORTABLE_MODELS = [
        Language::class => "Языки программирования",
        Construction::class => "Языковые конструкции",
    ];

    /**
     * Возвращает массив моделей, доступных для импорта пользователем
     * @return array
     */
    public function getAllowedModels(): array
    {
        $result = [];
        foreach (self::IMPORTABLE_MODELS as $modelClass => $modelName) {
            if (!Auth::user()?->can('admin.import.model', $modelClass)) {
                continue;
            }
            $result[] = [
                'title' => $modelName,
                'value' => $modelClass,
            ];
        }

        return $result;
    }
}

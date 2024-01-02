<?php

namespace App\Services;

use App\Models\Construction;
use App\Models\Language;
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
            if (!$this->canImport($modelClass)) {
                continue;
            }
            $result[] = [
                'title' => $modelName,
                'value' => $modelClass,
            ];
        }

        return $result;
    }

    /**
     * Может ли текущий пользователь импортировать модель
     * @param string $modelClass
     *
     * @return bool
     */
    public function canImport(string $modelClass): bool
    {
        return Auth::user()?->can('create', $modelClass);
    }
}

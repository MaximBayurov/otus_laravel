<?php

namespace App\Services;

use App\Models\Construction;
use App\Models\Language;
use Illuminate\Support\Facades\Auth;

class ExportService
{
    const EXPORTABLE_MODELS = [
        Language::class => "Языки программирования",
        Construction::class => "Языковые конструкции",
    ];

    /**
     * Возвращает массив моделей, доступных для экспорта пользователем
     * @return array
     */
    public function getAllowedModels(): array
    {
        $result = [];
        foreach (self::EXPORTABLE_MODELS as $modelClass => $modelName) {
            if (!$this->canExport($modelClass)) {
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
     * Может ли текущий пользователь экспортировать модель
     * @param string $modelClass
     *
     * @return bool
     */
    public function canExport(string $modelClass): bool
    {
        return Auth::user()?->can('viewAny', $modelClass);
    }
}

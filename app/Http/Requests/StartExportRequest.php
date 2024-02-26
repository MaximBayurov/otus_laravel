<?php

namespace App\Http\Requests;

use App\Services\ExportService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(ExportService $exportService): array
    {
        return [
            'email' => ['required', 'email'],
            'entity' => ['required', Rule::in(array_column($exportService->getAllowedModels(), 'value'))],
            'redo' => ['required', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            "redo" => $this->exists('redo'),
        ]);
    }
}

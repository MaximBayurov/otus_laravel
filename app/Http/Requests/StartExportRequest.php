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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $exportService = \App::get(ExportService::class);

        return [
            'email' => ['required', 'email'],
            'entity' => ['required', Rule::in(array_column($exportService->getAllowedModels(), 'value'))],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (!$this->exists('redo')) {
            $this->merge([
                "redo" => false,
            ]);
        }
    }
}

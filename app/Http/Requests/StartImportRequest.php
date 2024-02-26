<?php

namespace App\Http\Requests;

use App\Services\ImportService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartImportRequest extends FormRequest
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
    public function rules(ImportService $importService): array
    {
        return [
            'email' => ['required', 'email'],
            'entity' => ['required', Rule::in(array_column($importService->getAllowedModels(), 'value'))],
            'fields' => ['required', 'array'],
            'file' => ['required', 'mimes:csv,txt'],
            'withHeaders' => ['required', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            "withHeaders" => $this->exists('withHeaders'),
        ]);
    }
}

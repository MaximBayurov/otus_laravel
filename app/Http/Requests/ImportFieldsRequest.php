<?php

namespace App\Http\Requests;

use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportFieldsRequest extends FormRequest
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
        $importService = \App::get(ImportService::class);

        return [
            'model' => ['required', Rule::in(array_column($importService->getAllowedModels(), 'value'))],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
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
        $rules = [
            'slug' => ['required', 'max:255', 'unique:App\Models\Language,slug'],
            'title' => ['required', 'max:255'],
            'description' => ['max:65535'],
        ];

        $requestConstructions = $this->get('constructions');
        if (!empty($requestConstructions)
            && (!empty(reset($requestConstructions)['id'])
                || !empty(reset($requestConstructions)['code']))) {

            $rules['constructions.*.id'] = ['exists:App\Models\Construction,id'];
            $rules['constructions.*.code'] = ['required'];
        }

        return $rules;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConstructionRequest extends FormRequest
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
            'id' => 'required',
            'slug' => [
                'required',
                'max:255',
                Rule::unique('constructions')->ignore($this->get('id')),
            ],
            'title' => ['required', 'max:255'],
            'description' => ['max:65535'],
        ];

        $requestLanguages = $this->get('languages');
        if (!empty($requestLanguages)
            && (!empty(reset($requestLanguages)['id'])
                || !empty(reset($requestLanguages)['code']))) {

            $rules['languages.*.id'] = ['exists:App\Models\Language,id'];
            $rules['languages.*.code'] = ['required'];
        }

        return $rules;
    }
}

<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for updating a post. All fields are optional (sometimes = validate when present).
 */
class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'tags' => ['sometimes', 'required', 'array', 'min:1'],
            'tags.*' => ['required', 'string', 'max:50'],
        ];
    }
}

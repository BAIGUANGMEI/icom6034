<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for post image upload (multipart/form-data, field name: image).
 * Max 5MB; stored locally and URL returned for embedding in rich text.
 */
class UploadPostImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'], // 5MB
        ];
    }
}

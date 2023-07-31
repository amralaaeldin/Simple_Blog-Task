<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \App\Exceptions\BadRequestException($validator->errors()->toJson());
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:posts,title',
            'body' => 'required|string',
            'is_pinned' => 'boolean',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'tagNames' => 'array',
            'tagNames.*' => 'string|max:255'
        ];
    }
}

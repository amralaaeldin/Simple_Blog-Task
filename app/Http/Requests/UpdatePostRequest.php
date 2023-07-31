<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdatePostRequest extends FormRequest
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
            'title' => ['string', 'max:255', 'unique:posts,title,' . $this->id],
            'body' => 'string',
            'is_pinned' => 'boolean',
            'cover_image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'tagNames' => 'array',
            'tagNames.*' => 'string|max:255'
        ];
    }
}

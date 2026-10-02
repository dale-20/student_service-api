<?php

namespace App\Http\Requests;

use App\Enums\RecoverableType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecycleBinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(RecoverableType::class)],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }
}

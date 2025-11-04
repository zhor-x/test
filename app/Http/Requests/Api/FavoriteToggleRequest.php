<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteToggleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:questions,id',
        ];
    }
}

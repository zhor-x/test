<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FavoriteQuestionsRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'questions_id' => ['array']
        ];
    }
}

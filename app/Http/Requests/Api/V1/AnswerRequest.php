<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class
AnswerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'answer_id' => 'required|exists:answers,id',
            'questions_count' => 'required|integer|min:1',
            'is_right' => 'required|boolean',
        ];
    }
}

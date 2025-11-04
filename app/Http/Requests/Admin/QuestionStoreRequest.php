<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuestionStoreRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'question' => 'required|string|unique:question_translations,title',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'answers' => 'required|array|min:2|max:8', // Ensure at least 2 answers, max 8
            'answers.*.option_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'group_id' => 'required|integer|exists:groups,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'question' => 'հարց',
            'question_image' => 'Նկար',
            'answers.*' => 'Պատասխան',
            'correct_answer' => 'ճիշտ Պատասխան',
            'group_id' => 'խումբ',
        ];
    }
}

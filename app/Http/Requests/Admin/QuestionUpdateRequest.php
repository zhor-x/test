<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $questionId = $this->route('question');

        return [
            'question' => [
                'required',
                'string',
                Rule::unique('question_translations', 'title')
                    ->where('question_id', $questionId)
                    ->ignore($questionId, 'question_id'),
            ],
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'answers' => 'required|array|min:2|max:8', // Ensure at least 2 answers, max 8
            'answers.*.option_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'group_id' => 'required|integer|exists:groups,id',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
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

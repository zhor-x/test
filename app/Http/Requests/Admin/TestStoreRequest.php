<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                Rule::unique('test_translations', 'title')
            ],
            'duration' => 'required|integer|min:1',
            'max_wrong_answers' => 'required|integer|min:0',
            'hidden' => 'required|boolean',
            'questions' => 'nullable|array',
            'questions.*' => 'required|integer|exists:questions,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Վերնագիր',
            'duration' => 'Տևողություն (րոպե)',
            'max_wrong_answers.*' => 'Սխալ պատասխանների առավելագույն թիվ',
            'hidden' => 'Ցուցադրել',
            'questions' => 'Հարցեր',
        ];
    }
}

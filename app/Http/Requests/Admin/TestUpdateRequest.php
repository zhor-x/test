<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $testId = $this->route('test');

        return [
            'title' => [
                'required',
                'string',
                Rule::unique('test_translations', 'title')
                    ->where('test_id', $testId)
                    ->ignore($testId, 'test_id'),
            ],
            'duration' => 'required|integer|min:1',
            'max_wrong_answers' => 'required|integer|min:0',
            'hidden' => 'required|boolean',
            'test_id' => 'required|integer|exists:tests,id',
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
            'test_id' => 'հարցաշար',
            'questions' => 'Հարցեր',
        ];
    }
}

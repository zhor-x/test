<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $categoryId = $this->route('category');
        return [
             'title' => [
                'required',
                'string',
                Rule::unique('group_translations', 'title')
                    ->where('group_id', $categoryId)
                    ->ignore($categoryId, 'group_id'),
            ],
            'description' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'խումբ',
            'description' => 'նկարագրություն',
        ];
    }

}

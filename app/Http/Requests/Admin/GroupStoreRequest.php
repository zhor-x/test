<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GroupStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|unique:group_translations,title',
            'description' => 'required|string',
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

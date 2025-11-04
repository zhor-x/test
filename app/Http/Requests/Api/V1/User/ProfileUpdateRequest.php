<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'phone' => 'required',
            'email' => 'required|email|unique:users,email,' . auth('api')->user()->id,
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Էլ. հասցե',
            'firstName' => 'Անուն',
            'lastName' => 'Ազգանուն',
            'phone' => 'Հեռախոս',
        ];
    }
}

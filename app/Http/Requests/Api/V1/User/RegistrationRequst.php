<?php

namespace App\Http\Requests\Api\V1\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequst extends FormRequest
{

    public function rules(): array
    {
        return [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'password' => 'required|min:6',
            'role' => ['required', 'in:' . implode(',', array_column(UserRole::cases(), 'value'))],
            'phone' => 'required',
            'email' => 'required|email|unique:users,email',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Էլ. հասցե',
            'password' => 'Գաղտնաբառ',
            'role' => 'խումբ',
            'firstName' => 'Անուն',
            'lastName' => 'Ազգանուն',
            'phone' => 'Հեռախոս',
        ];
    }
}

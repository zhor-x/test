<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\UserDTO;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email,' . $this->user,
            'phone' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '',
            'email' => '',
            'phone' => '',
        ];
    }

    public function validated($key = null, $default = null): UserDTO
    {
        $validatedPayload = parent::validated($key, $default);

        return new UserDTO(
            $validatedPayload['name'],
            $validatedPayload['email'],
            $validatedPayload['phone'],
            UserRole::STUDENT
        );
    }

}

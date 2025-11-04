<?php

namespace App\Http\Requests\Admin;

use App\DTO\Admin\UserGroupDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserGroupStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                Rule::unique('user_groups', 'title')
            ],
            'users.*' => 'required|integer|exists:users,id',

        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Խումբ',
            'users' => 'Օգտատերեր',
        ];
    }

    public function validated($key = null, $default = null): UserGroupDTO
    {
        $validatedPayload = parent::validated($key, $default);

        return new UserGroupDTO(
            $validatedPayload['title'],
            $validatedPayload['users']??[],
        );
    }
}

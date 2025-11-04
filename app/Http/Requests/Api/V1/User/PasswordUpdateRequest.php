<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'oldPassword' => 'required|string|min:8',
            'newPassword' => 'required|string|min:8'
        ];
    }

    public function attributes(): array
    {
        return [
            'oldPassword' => 'Հին գաղտնաբառ',
            'newPassword' => 'Նոր գաղտնաբառ',
        ];
    }
}

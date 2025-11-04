<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequst extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean|nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Էլ. հասցե',
            'password' => 'Գաղտնաբառ',
        ];
    }
}

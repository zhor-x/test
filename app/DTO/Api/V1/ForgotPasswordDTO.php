<?php

namespace App\DTO\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordDTO
{
    public string $email;

    public function __construct(Request $request)
    {
        $this->validate($request);
        $this->email = $request->email;
    }

    protected function validate(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
             throw new \InvalidArgumentException($validator->errors()->first());
        }
    }
}

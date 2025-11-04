<?php

namespace App\DTO\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordDTO
{
    public string $token;
    public string $password;

    public function __construct(Request $request)
    {
        $this->validate($request);
        $this->token = $request->token;
        $this->password = $request->password;
    }

    protected function validate(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }
}

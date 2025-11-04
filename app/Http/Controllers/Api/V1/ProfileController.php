<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\User\PasswordUpdateRequest;
use App\Http\Requests\Api\V1\User\ProfileUpdateRequest;
use App\Services\Api\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Exception;

class ProfileController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile($request->validated());

        return response()->json($user);
    }

    public function changePassword(PasswordUpdateRequest $request): JsonResponse
    {
        try {
            return $this->userService->updatePassword($request->validated());
        } catch (Exception $exception) {
            Log::info('user.password.change' . $exception);
            return response()->json(['message' => 'ԻՆչ որ բան այն չէ խնդրում ենք փորձել ավելի ուշ!', 400]);
        }
    }

}

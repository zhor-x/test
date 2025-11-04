<?php

namespace App\Services\Api\V1;

use App\Repositories\Api\V1\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function updateProfile(array $payload)
    {
        return $this->userRepository->updateProfile($payload);
    }

    public function updatePassword(array $payload)
    {
        $userId = auth('api')->user()->id;

        $user = $this->userRepository->getById($userId);

        if (!Hash::check($payload['oldPassword'], $user->password)) {
            return response()->json(['message' => 'Սխալ գաղտնաբառ'], 401);
        }

        $this->userRepository->updatePassword($user, $payload['newPassword']);
        return response()->json(['message' => 'Գաղտնաբառը հաջողությամբ փոխվեց!']);
    }
}

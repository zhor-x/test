<?php

namespace App\Services\Api\V1;

use App\Mail\PasswordMail;
use App\Repositories\Api\V1\UserRepositoryInterface;
use App\Repositories\PasswordResetRepositoryInterface;
use App\Traits\AuthTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class PasswordResetService
{

    use AuthTrait;

    protected int $tokenExpirationMinutes;

    public function __construct(
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private UserRepositoryInterface          $userRepository
    )
    {
        $this->tokenExpirationMinutes = config('auth.passwords.users.expire', 60);

    }


    public function initiatePasswordReset(string $email): void
    {
        $user = $this->userRepository->getByEmail($email);

        $token = $this->resetPasswordToken($email);

        Mail::to($user->email)->send(new PasswordMail($user, $token));
    }


    public function resetPassword(string $token, string $password): void
    {
        $reset = $this->passwordResetRepository->findByEmailAndToken($token);

        if (!$reset || now()->diffInMinutes($reset->created_at) > $this->tokenExpirationMinutes) {
            throw new InvalidArgumentException('Անվավեր կամ ժամկետանց տոկեն');
        }

        $user = $this->userRepository->getByEmail($reset->email);

        $this->userRepository->updatePassword($user, Hash::make($password));
        $this->passwordResetRepository->deleteByEmail($reset->email);
    }


}

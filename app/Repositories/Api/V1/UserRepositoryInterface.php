<?php

namespace App\Repositories\Api\V1;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create($payload): User;

    public function getByEmail(string $email): User;
    public function getById(int $userId): User;

    public function updatePassword(User $user, string $password): void;

    public function updateProfile(array $payload): User;

}

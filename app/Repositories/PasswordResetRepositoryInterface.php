<?php

namespace App\Repositories;

interface
PasswordResetRepositoryInterface
{
    public function createOrUpdate(array $data): void;
    public function findByEmailAndToken(string $token): ?object;
    public function deleteByEmail(string $email): void;
}

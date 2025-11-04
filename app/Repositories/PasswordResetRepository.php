<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PasswordResetRepository implements  PasswordResetRepositoryInterface
{
    protected string $table = 'password_reset_tokens';

    /**
     * Create or update password reset token
     *
     * @param array $data
     * @return void
     */
    public function createOrUpdate(array $data): void
    {
        DB::table($this->table)->updateOrInsert(
            ['email' => $data['email']],
            $data
        );
    }

    /**
     * Find password reset by email and token
     *
     * @param string $email
     * @param string $token
     * @return object|null
     */
    public function findByEmailAndToken(string $token): ?object
    {
        return DB::table($this->table)
             ->where('token', $token)
            ->first();
    }

    /**
     * Delete password reset tokens by email
     *
     * @param string $email
     * @return void
     */
    public function deleteByEmail(string $email): void
    {
        DB::table($this->table)->where('email', $email)->delete();
    }
}

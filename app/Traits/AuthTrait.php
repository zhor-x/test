<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait AuthTrait
{
    private function generateTokenResponse(User $user, bool $remember = false): array
    {
//        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->accessToken;
        $refreshToken = Str::random(60);

        $user->update([
            'refresh_token' => Hash::make($refreshToken),
            'refresh_token_expires_at' => Carbon::now()->addDays($remember ? 30 : 7),
        ]);

        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'token' => $token,
            'refresh_token' => $refreshToken,
            'expires_at' => Carbon::now()->addDay()->timestamp,
            'refresh_expires_at' => $user->refresh_token_expires_at->timestamp,
        ];
    }

    private function resetPasswordToken($email): string
    {
        $token = Str::random(60); // Генерируем одноразовый токен

        // Сохраняем токен в таблице password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        return $token;
    }
}

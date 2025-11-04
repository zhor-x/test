<?php

namespace App\Repositories\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{

    public function create($payload): User
    {
        return User::create([
            'first_name' => $payload['firstName'],
            'last_name' => $payload['lastName'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'role' => $payload['role'],
            'password' => Hash::make($payload['password']),
        ]);
    }

    public function getByEmail(string $email): User
    {
        return User::query()->where('email', $email)->first();
    }


    public function getById(int $userId): User
    {
        return User::query()->findOrFail($userId);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = $password;
        $user->save();
    }

    public function updateProfile(array $payload): User
    {
        $userId = auth('api')->user()->id;
        $user = User::findOrFail($userId);

        $user->first_name = $payload['firstName'];
        $user->last_name = $payload['lastName'];
        $user->phone = $payload['phone'];
        $user->email = $payload['email'];

        $user->save();
        return $user;
    }
}

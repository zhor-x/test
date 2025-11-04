<?php

namespace App\Repositories\Admin\Users;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserDTO;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator
    {
        $questions =  User::search($payload->getQuery())
            ->query(function ($query) {
                $query->where('role', UserRole::STUDENT)
                     ->orderByDesc('id');
            });

        if ($payload->getLimit() === 'all') {
            return $questions->get();
        }

        return $questions->paginate($payload->getLimit());
    }

    public function store(UserDTO $payload): User
    {
        $user = new User;
        $user->name = $payload->getName();
        $user->email = $payload->getEmail();
        $user->phone = $payload->getPhone();
        $user->role = $payload->getRole();
        $user->password = Hash::make(rand(1,1000));
        $user->save();

        return $user;
    }

    public function getById(int $id): User
    {
        return User::query()->findOrFail($id);
    }


    public function update(UserDTO $payload, int $userId): void
    {
        $user = $this->getById($userId);
        $user->name = $payload->getName();
        $user->email = $payload->getEmail();
        $user->phone = $payload->getPhone();
        $user->save();
    }


    public function destroy(int $userId): void
    {
        $user = $this->getById($userId);
        $user->delete();
    }

    public function groupUserList(ListDTO $payload): Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $questions =  User::search($payload->getQuery())
            ->query(function ($query) {
                $query->where('role', UserRole::STUDENT)
                    ->whereDoesntHave('userGroups')
                    ->orderByDesc('id');
            });

        if ($payload->getLimit() === 'all') {
            return $questions->get();
        }

        return $questions->paginate($payload->getLimit());
    }
}

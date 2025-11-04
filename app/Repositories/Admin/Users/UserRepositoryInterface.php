<?php

namespace App\Repositories\Admin\Users;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator;

    public function store(UserDTO $payload): User;

    public function getById(int $id): User;

    public function update(  UserDTO $payload, int $userId): void;

    public function destroy(int $userId): void;

}

<?php

namespace App\Repositories\Admin\UsersGroups;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserGroupDTO;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserGroupRepositoryInterface
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator;

    public function store(UserGroupDTO $payload): UserGroup;

    public function getById(int $id): UserGroup;

    public function update(UserGroupDTO $payload, int $userId): void;

    public function destroy(int $userGroupId): void;

}

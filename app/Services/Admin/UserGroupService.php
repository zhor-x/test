<?php

namespace App\Services\Admin;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserDTO;
use App\DTO\Admin\UserGroupDTO;
use App\Models\UserGroup;
use App\Repositories\Admin\UsersGroups\UserGroupRepositoryInterface;
use App\Traits\AuthTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class UserGroupService
{

    use AuthTrait;

    public function __construct(private UserGroupRepositoryInterface $repository)
    {
    }

    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator
    {
        return $this->repository->getPagination($payload);
    }

    public function getById(int $groupId): UserGroup
    {
        return $this->repository->getById($groupId);
    }


    public function store(UserGroupDTO $payload): void
    {
        $this->repository->store($payload);
    }


    public function update(UserGroupDTO $payload, int $userId): void
    {
        $this->repository->update($payload, $userId);
    }


    public function destroy(int $userGroupId): void
    {
        $this->repository->destroy($userGroupId);
    }
}

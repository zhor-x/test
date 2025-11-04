<?php

namespace App\Services\Admin;

use App\Models\Group;
use App\Repositories\Admin\Groups\AdminGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class GroupService
{
    public function __construct(private AdminGroupRepositoryInterface $repository)
    {
    }

    public function getAllgroups(): Collection
    {
        return $this->repository->getAllWithTranslations();
    }

    public function getById(int $groupId)
    {
        return $this->repository->getById($groupId);
    }


    public function store(array $payload): Group
    {
        return $this->repository->store($payload);
    }


    public function update(array $payload, int $groupId): Group
    {
        return $this->repository->update($payload, $groupId);
    }

    public function destroy(int $groupId): void
    {
         $this->repository->destroy($groupId);
    }
}

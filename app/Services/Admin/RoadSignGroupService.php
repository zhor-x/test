<?php

namespace App\Services\Admin;

use App\Models\Group;
use App\Models\RoadSignCategory;
use App\Repositories\Admin\Groups\AdminGroupRepositoryInterface;
use App\Repositories\Admin\RoadSignGroups\AdminSignGroupRepository;
use App\Repositories\Admin\RoadSignGroups\AdminSignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

readonly class RoadSignGroupService
{
    public function __construct(private AdminSignRepositoryInterface $repository)
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


    public function store(array $payload): RoadSignCategory
    {
        return $this->repository->store($payload);
    }


    public function update(array $payload, int $groupId): RoadSignCategory
    {
        return $this->repository->update($payload, $groupId);
    }

    public function destroy(int $groupId): void
    {
         $this->repository->destroy($groupId);
    }
}

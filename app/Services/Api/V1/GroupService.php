<?php

namespace App\Services\Api\V1;

use App\Repositories\Api\V1\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class GroupService
{
    public function __construct(private GroupRepositoryInterface $repository)
    {
    }

    public function getAllgroups(): Collection
    {
        return $this->repository->getAllWithTranslations();
    }

    public function getQuestionsByGroupId(int $groupId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getQuestionsByGroupId($groupId, $perPage);
    }

    public function getQuestionsByGroupIdClean(int $groupId): LengthAwarePaginator
    {
        return $this->repository->getQuestionsByGroupIdClean($groupId);
    }
}

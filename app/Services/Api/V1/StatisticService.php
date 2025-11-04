<?php

namespace App\Services\Api\V1;

use App\Repositories\Api\V1\StatisticRepository;
use App\Repositories\Api\V1\StatisticRepositoryInterface;

readonly class StatisticService
{
    public function __construct(private StatisticRepositoryInterface $repository)
    {
    }

    public function getUserWrongList()
    {
        return $this->repository->getUserWrongList();
    }

    public function submitStatistic(array $payload): void
    {
        $this->repository->submit($payload);
    }

    public function updateStatistic(int $id, bool $isCorrect): void
    {
        $this->repository->updateStatistic($id, $isCorrect);
    }

    public function getGroupUserList(array $payload)
    {
        return $this->repository->getGroupUserList($payload);
    }

    public function getUserCategoryList(int $userId)
    {
         return $this->repository->getUserCategoryList($userId);

    }

    public function getQuestionsByDate(int $userId, string $date)
    {
         return $this->repository->getByDate($userId, $date);
    }
}

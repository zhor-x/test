<?php

namespace App\Repositories\Api\V1;

interface StatisticRepositoryInterface
{
    public function getUserWrongList();

    public function submit(array $payload): void;

    public function updateStatistic(int $id, bool $isCorrect): void;
    public function getGroupUserList($payload);
    public function getUserCategoryList(int $userId);
    public function getByDate(int $userId, string $date);
}

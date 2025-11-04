<?php

namespace App\Services\Admin;

use App\DTO\Admin\ListDTO;
use App\Models\Test;
use App\Repositories\Admin\Tests\AdminTestRepositoryInterFace;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class AdminTestService
{
    public function __construct(private AdminTestRepositoryInterFace $repository)
    {
    }

    public function getPagination(ListDTO $listDTO): LengthAwarePaginator
    {
        return $this->repository->getPagination($listDTO);
    }

    public function getById(int $groupId): Test
    {
        $question =  $this->repository->getById($groupId);

        $question->load('questions');

        return $question;
    }


    public function store(array $payload): Test
    {
        return $this->repository->store($payload);
    }


    public function update(array $payload, int $testId): Test
    {
        return $this->repository->update($payload, $testId);
    }

    public function destroy(int $groupId): void
    {
         $this->repository->destroy($groupId);
    }
}

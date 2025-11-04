<?php

namespace App\Services\Admin;

use App\Models\Question;
use App\Repositories\Admin\Questions\QuestionRepositoryInterFace;
use App\Repositories\Admin\RoadSign\RoadSignRepositoryInterFace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class RoadSignService
{
    public function __construct(private RoadSignRepositoryInterFace $repository)
    {
    }

    public function getPagination($payload): Collection|LengthAwarePaginator
    {
        return $this->repository->getPagination($payload);
    }

    public function getById(int $groupId)
    {
        $question =  $this->repository->getById($groupId);

        $question->load('answers.translation');

        return $question;
    }


    public function store(array $payload): Question
    {
        return $this->repository->store($payload);
    }


    public function update(array $payload, int $groupId): Question
    {
        return $this->repository->update($payload, $groupId);
    }

    public function destroy(int $groupId): void
    {
         $this->repository->destroy($groupId);
    }
}

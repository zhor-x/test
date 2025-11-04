<?php

namespace App\Repositories\Admin\RoadSign;

use App\DTO\Admin\ListDTO;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoadSignRepositoryInterFace
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator;

    public function getById(int $questionId): Question;

    public function store(array $payload): Question;

    public function update(array $payload, int $questionId): Question;

    public function destroy( int $questionId): void;
}

<?php

namespace App\Repositories\Admin\Tests;

use App\DTO\Admin\ListDTO;
use App\Models\Test;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminTestRepositoryInterFace
{
    public function getPagination(ListDTO $listDTO): LengthAwarePaginator;

    public function getById(int $questionId): Test;

    public function store(array $payload): Test;

    public function update(array $payload, int $testId): Test;

    public function destroy( int $questionId): void;
}

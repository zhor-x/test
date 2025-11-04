<?php

namespace App\Repositories\Api\V1;

use App\Models\Test;
use Illuminate\Database\Eloquent\Collection;

interface ExamTestRepositoryInterface
{
    public function getAllWithTranslations(): Collection;

    public function findByUniqueId(string $uniqueId);

    public function getById(int $testId): Test;

    public function getByUserId(int $userId);

    public function getTestByUuId(string $uniqueId);

}

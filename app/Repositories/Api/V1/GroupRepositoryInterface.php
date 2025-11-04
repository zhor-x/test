<?php

namespace App\Repositories\Api\V1;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface GroupRepositoryInterface
{
    public function getAllWithTranslations(): Collection;
    public function getQuestionsByGroupId(int $groupId): LengthAwarePaginator;
    public function getQuestionsByGroupIdClean(int $groupId): LengthAwarePaginator;
}

<?php

namespace App\Repositories\Api\V1;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupRepository implements GroupRepositoryInterface
{
    public function getAllWithTranslations(): Collection
    {
        return Group::with(['translation'])->get();
    }

    public function getQuestionsByGroupId(int $groupId, int $perPage = 10): LengthAwarePaginator
    {
        return Group::findOrFail($groupId)
            ->questions()
            ->with([
                'translation',
                'translations.language',
                'answers.translation',
                'answers.translations.language'
            ])
            ->whereHas('translation')
            ->paginate(200);
    }

    public function getQuestionsByGroupIdClean(int $groupId, int $perPage = 10): LengthAwarePaginator
    {
        return Group::findOrFail($groupId)
            ->questions()
            ->paginate(200);
    }

}

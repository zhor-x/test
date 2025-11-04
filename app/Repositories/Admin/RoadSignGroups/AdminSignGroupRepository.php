<?php

namespace App\Repositories\Admin\RoadSignGroups;

use App\Models\RoadSignCategory;
use Illuminate\Database\Eloquent\Collection;

class AdminSignGroupRepository implements AdminSignRepositoryInterface
{
    public function getAllWithTranslations(): Collection
    {
        return RoadSignCategory::with(['translation'])->get();
    }

    public function getById(int $groupId): RoadSignCategory
    {
        return RoadSignCategory::with('translation')->findOrFail($groupId);
    }

    public function store(array $payload): RoadSignCategory
    {
        $group = RoadSignCategory::create();

        $group->translation()->create([
            ...$payload,
            'language_id' => 102,
        ]);

        return $this->getById($group->id);
    }

    public function update(array $payload, int $groupId): RoadSignCategory
    {
        $group = $this->getById($groupId);

        $group->translation()->where('language_id', 102)
            ->update($payload);

        return $group;
    }

    public function destroy(int $groupId): void
    {
        $group = $this->getById($groupId);

        $group->translation()->delete();

        $group->delete();
    }
}

<?php

namespace App\Repositories\Admin\Groups;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class AdminAdminGroupRepository implements AdminGroupRepositoryInterface
{
    public function getAllWithTranslations(): Collection
    {
        return Group::with(['translation'])->get();
    }

    public function getById(int $groupId): Group
    {
        return Group::with('translation')->findOrFail($groupId);
    }

    public function store(array $payload): Group
    {
        $group = Group::create();

        $group->translation()->create([
            ...$payload,
            'language_id' => 102,
        ]);

        return $this->getById($group->id);
    }

    public function update(array $payload, int $groupId): Group
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

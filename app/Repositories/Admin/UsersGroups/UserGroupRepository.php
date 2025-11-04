<?php

namespace App\Repositories\Admin\UsersGroups;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserGroupDTO;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserGroupRepository implements UserGroupRepositoryInterface
{
    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator
    {
        $questions = UserGroup::search($payload->getQuery())
            ->query(function ($query) {
                $query->withCount(['users'])
                    ->orderByDesc('id');
            });

        if ($payload->getLimit() === 'all') {
            return $questions->get();
        }

        return $questions->paginate($payload->getLimit());

    }

    public function store(UserGroupDTO $payload): UserGroup
    {
        $userGroup = new UserGroup();
        $userGroup->title = $payload->getTitle();
        $userGroup->save();

        $userGroup->users()->sync($payload->getUsers());

        return $userGroup;
    }

    public function getById(int $id): UserGroup
    {
        return UserGroup::query()->with('users')->findOrFail($id);
    }


    public function update(UserGroupDTO $payload, int $userId): void
    {
        $userGroup = $this->getById($userId);
        $userGroup->title = $payload->getTitle();
        $userGroup->save();

        $userGroup->users()->sync($payload->getUsers());
    }


    public function destroy(int $userGroupId): void
    {
        $this->getById($userGroupId)->delete();
    }
}

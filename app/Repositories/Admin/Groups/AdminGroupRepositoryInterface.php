<?php

namespace App\Repositories\Admin\Groups;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface AdminGroupRepositoryInterface
{
    public function getAllWithTranslations(): Collection;
    public function getById(int $groupId): Group;
    public function store(array $payload): Group;

    public function update(array $payload, int $groupId): Group;
    public function destroy( int $groupId): void;
 }

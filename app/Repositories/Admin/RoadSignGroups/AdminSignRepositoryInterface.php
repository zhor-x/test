<?php

namespace App\Repositories\Admin\RoadSignGroups;

use App\Models\RoadSignCategory;
use Illuminate\Database\Eloquent\Collection;

interface AdminSignRepositoryInterface
{
    public function getAllWithTranslations(): Collection;
    public function getById(int $groupId): RoadSignCategory;
    public function store(array $payload): RoadSignCategory;

    public function update(array $payload, int $groupId): RoadSignCategory;
    public function destroy( int $groupId): void;
 }

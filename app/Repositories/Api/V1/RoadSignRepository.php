<?php

namespace App\Repositories\Api\V1;

use App\Models\RoadSignCategory;
use Illuminate\Database\Eloquent\Collection;

class RoadSignRepository implements RoadSignRepositoryInterface
{
    public function getSingsWithCategory(): Collection
    {
        return RoadSignCategory::query()
            ->whereHas('roadSings.translation')
            ->whereHas('translation')
            ->with(['roadSings.translation', 'translation'])
            ->get();
    }
}

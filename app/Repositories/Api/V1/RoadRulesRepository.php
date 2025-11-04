<?php

namespace App\Repositories\Api\V1;

use App\Models\PddChapters;
use Illuminate\Database\Eloquent\Collection;

class RoadRulesRepository implements RoadRulesRepositoryInterface
{
    public function getRulesWithChapters(): Collection
    {
        return PddChapters::query()
            ->whereHas('rules.translation')
            ->whereHas('translation')
            ->with([
                'translation',
                'rules' => function ($query) {
                    $query->whereHas('translation'); // only load rules that have translation
                },
                'rules.translation'
            ])
            ->get();
    }
}

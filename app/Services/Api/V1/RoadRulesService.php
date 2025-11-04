<?php

namespace App\Services\Api\V1;

use App\Repositories\Api\V1\RoadRulesRepositoryInterface;

readonly class RoadRulesService
{
    public function __construct(private RoadRulesRepositoryInterface $repository)
    {
    }

    public function getRulesWithChapters()
    {
        return $this->repository->getRulesWithChapters();
     }
}

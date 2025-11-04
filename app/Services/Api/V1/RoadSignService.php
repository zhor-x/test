<?php

namespace App\Services\Api\V1;

use App\Models\UserExamTest;
use App\Repositories\Admin\RoadSignGroups\AdminSignRepositoryInterface;
use App\Repositories\Api\V1\ExamTestRepositoryInterface;
use App\Repositories\Api\V1\RoadSignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

readonly class RoadSignService
{
    public function __construct(private RoadSignRepositoryInterface $repository)
    {
    }

    public function getSingsWithCategory()
    {
        $categories =  $this->repository->getSingsWithCategory();


        return $categories->each(function ($category) {
            $category->roadSings = collect($category->roadSings)->sort(function ($a, $b) {
                return version_compare($a->translation->title, $b->translation->title);
            })->values();
        });
     }
}

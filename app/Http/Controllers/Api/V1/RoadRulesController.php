<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RoadRulesChapterResource;
use App\Services\Api\V1\RoadRulesService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoadRulesController extends Controller
{
    public function index(RoadRulesService $roadRulesService): AnonymousResourceCollection
    {
        $rules = $roadRulesService->getRulesWithChapters();

        return RoadRulesChapterResource::collection($rules);
    }
}

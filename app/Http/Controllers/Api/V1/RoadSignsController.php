<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RoadSignCategoryResource;
use App\Http\Resources\Api\V1\RoadSignResource;
use App\Services\Api\V1\RoadSignService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoadSignsController extends Controller
{
    public function index(RoadSignService $roadSignService):AnonymousResourceCollection
    {
        $roadSigns = $roadSignService->getSingsWithCategory();

        return RoadSignCategoryResource::collection($roadSigns);
    }
}

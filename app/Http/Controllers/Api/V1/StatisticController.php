<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchRequest;
use App\Http\Requests\Api\V1\StoreStatisticRequest;
use App\Http\Resources\Api\V1\StatisticGroupResource;
use App\Http\Resources\Api\V1\StatisticQuestionsResource;
use App\Http\Resources\Api\V1\StatisticResource;
use App\Http\Resources\Api\V1\StatisticUserResource;
use App\Services\Api\V1\StatisticService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use function Symfony\Component\String\s;

class StatisticController extends Controller
{
    public function __construct(private readonly StatisticService $service)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $statistics = $this->service->getUserWrongList();

        return StatisticResource::collection($statistics);

    }

    public function store(StoreStatisticRequest $request): void
    {
        $this->service->submitStatistic($request->validated());
    }

    public function updateStatistic(int $id, bool $isCorrect)
    {
         $this->service->updateStatistic($id, $isCorrect);
    }

    public function getGroupUserList(SearchRequest $request)
    {
        $statistics = $this->service->getGroupUserList($request->validated());

        return StatisticUserResource::collection($statistics);
    }

    public function getUserGroupList(int $userId): AnonymousResourceCollection
    {
        $statistics = $this->service->getUserCategoryList($userId);

        return StatisticGroupResource::collection($statistics);
    }

    public function getByDate(int $userId, string $date): AnonymousResourceCollection
    {
        $groupedStats = $this->service->getQuestionsByDate($userId, $date);


        return $groupedStats;
        return $groupedStats->map(function ($items, $groupId) {
            return [
                'group_id' => $groupId,
                'questions' => StatisticQuestionsResource::collection($items),
            ];
        })->values();

     }
}

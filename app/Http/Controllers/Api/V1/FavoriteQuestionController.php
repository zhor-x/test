<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FavoriteQuestionsRequest;
use App\Http\Requests\Api\FavoriteToggleRequest;
use App\Http\Resources\Api\V1\GroupQuestionResource;
use App\Services\Api\V1\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteQuestionController extends Controller
{
    public function __construct(private FavoriteService $favoriteService)
    {
    }

    public function toggle(FavoriteToggleRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $status = $this->favoriteService->toggleFavorite($payload);

        return response()->json($status);
    }

    public function list(): JsonResponse
    {
        $favorites = $this->favoriteService->getFavoriteList();

        return response()->json($favorites);
    }

    public function getFavoriteQuestions(FavoriteQuestionsRequest $request): AnonymousResourceCollection
    {
        $questions = $this->favoriteService->getFavoriteQuestions($request->validated()['questions_id']);

        return GroupQuestionResource::collection($questions);
    }
}

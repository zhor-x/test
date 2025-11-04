<?php

namespace App\Services\Api\V1;

use App\Repositories\Admin\Questions\QuestionRepositoryInterFace;
use App\Repositories\Api\V1\FavoriteQuestionInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function __construct(private FavoriteQuestionInterface $favoriteQuestion, private QuestionRepositoryInterFace $questionRepository)
    {
    }

    public function toggleFavorite($payload): array
    {
        $userId = Auth::user()->id;
        $questionId = $payload['question_id'];
        $favorite = $this->favoriteQuestion->getFavorite($userId, $questionId);

        if ($favorite) {
            $favorite->delete();

            return ['status' => 'removed'];
        } else {
            $this->favoriteQuestion->create($userId, $questionId);

            return ['status' => 'added'];
        }
    }

    public function getFavoriteList(): Collection
    {
        return $this->favoriteQuestion->getFavorites();
    }

    public function getFavoriteQuestions($questions)
    {
        return $this->questionRepository->findByIds($questions);
    }
}

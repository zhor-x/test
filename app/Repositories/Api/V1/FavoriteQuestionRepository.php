<?php

namespace App\Repositories\Api\V1;

use App\Models\FavoriteQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FavoriteQuestionRepository implements FavoriteQuestionInterface
{
    public function getFavorite($userId, $questionId)
    {
        return FavoriteQuestion::query()
            ->where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function create($userId, $questionId): void
    {
        $favorite = new FavoriteQuestion;
        $favorite->user_id = $userId;
        $favorite->question_id = $questionId;
        $favorite->save();
    }

    public function getFavorites(): Collection
    {
        return FavoriteQuestion::with('question')
            ->where('user_id', Auth::id())
            ->get();
    }


    public function getUserQuestions($userId, $questions): Collection
    {
        return FavoriteQuestion::query()
            ->where('user_id', $userId)
            ->whereIn('question_id', $questions)
            ->with(['questions.translation', 'question.answers.translation'])
            ->get();
    }
}

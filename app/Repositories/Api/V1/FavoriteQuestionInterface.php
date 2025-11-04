<?php

namespace App\Repositories\Api\V1;

use Illuminate\Database\Eloquent\Collection;

interface FavoriteQuestionInterface
{
    public function getFavorite($userId, $questionId);

    public function create($userId, $questionId): void;

    public function getFavorites(): Collection;

    public function getUserQuestions($userId, $questions): Collection;

}

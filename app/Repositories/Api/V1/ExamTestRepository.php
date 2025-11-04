<?php

namespace App\Repositories\Api\V1;

use App\Enums\UserRole;
use App\Models\Test;
use App\Models\UserExamTest;
use Illuminate\Database\Eloquent\Collection;

class ExamTestRepository implements ExamTestRepositoryInterface
{
    public function getAllWithTranslations(): Collection
    {
        $examTests = Test::with(['translation'])->whereHas('translation');
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $examTests = $examTests->with([
              'userExamTests' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                         ->where('is_completed', true)
                        ->orderBy('created_at', 'desc')
                        ->limit(3)
                        ->withCount(['questions as correct_questions_count' => function ($q) {
                            $q->where('is_right', true);
                        }]);
                }
            ])->withCount('questions as questions_count');
        }

        return $examTests->get();
    }

    public function findByUniqueId(string $uniqueId)
    {
        return UserExamTest::where('unique_id', $uniqueId)
            ->with([
                'examTest.questions.translation',
                'examTest.questions.answers.translation',
                'examTest.questions.explanation.translation',
                'examTest.questions',
             ])
            ->first();
    }

    public function getById(int $testId): Test
    {
        return Test::findOrFail($testId);
    }

    public function getByUserId(int $userId)
    {
        return UserExamTest::where(['user_id' => $userId, 'is_completed' => true])
            ->with(['examTest.translation'])
            ->withCount(['questions as correct_questions_count' => function ($q) {
                $q->where('is_right', true);
            }])
            ->withCount('questions as questions_count')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTestByUuId(string $uniqueId)
    {
        $user = auth('api')->user();
        $test = $this->findByUniqueId($uniqueId);
        $test->load('questions');

        if ($user &&($user->role !== UserRole::ADMIN->value && $test->user_id !== $user->id)) {
            return null;
        }

        return $test;
    }
}

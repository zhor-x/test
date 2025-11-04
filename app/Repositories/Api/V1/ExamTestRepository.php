<?php

namespace App\Repositories\Api\V1;

use App\Enums\UserRole;
use App\Models\Language;
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

    public function findByUniqueId(string $uniqueId, ?string $locale = null)
    {
        $language = Language::resolveByCode($locale ?? app()->getLocale());
        $fallback = Language::fallback();

        $applyTranslationScope = function ($query) use ($language, $fallback) {
            Language::applyTranslationScope($query, $language, $fallback);
        };

        return UserExamTest::where('unique_id', $uniqueId)
            ->with([
                'examTest' => function ($examTestQuery) use ($applyTranslationScope) {
                    $examTestQuery->with([
                        'translation' => $applyTranslationScope,
                        'questions' => function ($questionQuery) use ($applyTranslationScope) {
                            $questionQuery->with([
                                'translation' => $applyTranslationScope,
                                'answers' => function ($answerQuery) use ($applyTranslationScope) {
                                    $answerQuery->with(['translation' => $applyTranslationScope]);
                                },
                                'explanation' => function ($explanationQuery) use ($applyTranslationScope) {
                                    $explanationQuery->with(['translation' => $applyTranslationScope]);
                                },
                                'group' => function ($groupQuery) use ($applyTranslationScope) {
                                    $groupQuery->with(['translation' => $applyTranslationScope]);
                                },
                            ]);
                        },
                    ]);
                },
                'questions' => function ($query) {
                    $query->with(['question', 'answer']);
                },
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

    public function getTestByUuId(string $uniqueId, ?string $locale = null)
    {
        $user = auth('api')->user();
        $test = $this->findByUniqueId($uniqueId, $locale);

        if (!$test) {
            return null;
        }

        $test->load('questions');

        if ($user &&($user->role !== UserRole::ADMIN->value && $test->user_id !== $user->id)) {
            return null;
        }

        return $test;
    }
}

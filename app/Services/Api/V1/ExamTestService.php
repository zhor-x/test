<?php

namespace App\Services\Api\V1;

use App\Models\UserExamTest;
use App\Repositories\Api\V1\ExamTestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

readonly class ExamTestService
{
    public function __construct(private ExamTestRepositoryInterface $repository)
    {
    }

    public function getAllExamTests(): Collection
    {
        return $this->repository->getAllWithTranslations();
    }

    public function getUserExamTestByUniqueId(string $uniqueId)
    {
        return $this->repository->findByUniqueId($uniqueId);
    }

    public function submitAnswer(string $uniqueId, array $data): ?UserExamTest
    {
        $userExamTest = $this->repository->findByUniqueId($uniqueId);


        if (!$userExamTest) {
            return null;
        }

        $examTestQuestionId = $userExamTest->examTest->questions()->whereHas('answers', function ($q) use ($data) {
            $q->where('id', $data['answer_id']);
        })->first()->pivot->id;

        return DB::transaction(function () use ($userExamTest, $data, $examTestQuestionId) {

            $userExamTest->questions()->updateOrCreate(
                [
                    'user_exam_test_id' => $userExamTest->id,
                    'test_question_id' => $examTestQuestionId,
                ],
                [
                    'test_answer_id' => $data['answer_id'],
                    'is_right' => $data['is_right'],
                    'updated_at' => now(),
                ]
            );

            $totalQuestions = $data['questions_count'];
            $answeredQuestions = $userExamTest->questions()->count();

            $userExamTest->finish_time = $userExamTest->created_at->diffInSeconds(now());
            $userExamTest->is_completed = $answeredQuestions >= $totalQuestions;

            $userExamTest->save();

            return $userExamTest;
        });
    }

    public function submitFinal(string $uniqueId): void
    {
        $userExamTest = $this->repository->findByUniqueId($uniqueId);
        $userExamTest->finish_time = $userExamTest->created_at->diffInSeconds(now());
        $userExamTest->is_completed = true;
        $userExamTest->save();
    }

    public function generateUuId(int $testId)
    {
        $test = $this->repository->getById($testId);


        $examTest = $test->userExamTests()->create([
                'test_id' => $testId,
                'user_id' => auth('api')?->user()?->id??null,
                'is_completed' => false,
                'finish_time' => null,
            ]
        );

        return $examTest->unique_id;
    }

    public function getMyTests()
    {
        $userId = auth('api')->user()->id;

        return $this->repository->getByUserId($userId);
    }

    public function getTestsByUserId($userId)
    {
        return $this->repository->getByUserId($userId);
    }

    public function getTestByUuId(string $testUuid)
    {
        return $this->repository->getTestByUuId($testUuid);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AnswerRequest;
use App\Http\Resources\Api\V1\UserExamTestResource;
use App\Services\Api\V1\ExamTestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserExamTestController extends Controller
{

    public function __construct(private readonly ExamTestService $service)
    {
    }

    public function show(string $lang, string $uniqueId): JsonResponse
    {
        try {
            $userExamTest = $this->service->getUserExamTestByUniqueId($uniqueId, $lang);

            if (!$userExamTest) {
                return response()->json(['error' => 'User exam test not found'], 404);
            }
            return response()->json(['user_exam_test' => new UserExamTestResource($userExamTest)]);
        } catch (Exception $e) {
            Log::error('Error fetching user exam test: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function generateUuId(string $lang, int $testId): JsonResponse
    {
        return response()->json(['unique_id' => $this->service->generateUuId($testId)]);
    }

    public function submitAnswer(string $lang, string $uniqueId, AnswerRequest $request): JsonResponse
    {
         try {

            $validated = $request->validated();

            $userExamTest = $this->service->submitAnswer($uniqueId, $validated, $lang);

            if (!$userExamTest) {
                return response()->json(['error' => 'User exam test not found'], 404);
            }

            return response()->json([
                'user_exam_test' => new UserExamTestResource($userExamTest),
                'message' => 'Answer submitted successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error submitting answer: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function submitFinal(string $lang, string $uniqueId)
    {
        try {
            $this->service->submitFinal($uniqueId, $lang);
        } catch (Exception $e) {
            Log::error('Error submitting answer: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}

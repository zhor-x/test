<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Api\V1\ExamTestResource;
use App\Http\Resources\Api\V1\MyExamTestResource;
use App\Http\Resources\Api\V1\UserExamTestResource;
use App\Http\Resources\Api\V1\UserTestResource;
use App\Services\Api\V1\ExamTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExamTestController
{
    protected $service;

    public function __construct(ExamTestService $service)
    {
        $this->service = $service;
    }

    public function myTests(): AnonymousResourceCollection
    {
       return MyExamTestResource::collection($this->service->getMyTests());
    }

    public function testsByUserId($userId): AnonymousResourceCollection
    {
        return MyExamTestResource::collection($this->service->getTestsByUserId($userId));
    }

    public function testByUuId(string $lang, string $testUuid): UserTestResource
    {
        return new UserTestResource($this->service->getTestByUuId($testUuid, $lang));
    }

    public function index(): JsonResponse
    {
        $examTests = $this->service->getAllExamTests();

        return response()->json([
            'exam_tests' => ExamTestResource::collection($examTests),
            'status' => 'success'
        ]);
    }
}

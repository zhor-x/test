<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\QuestionStoreRequest;
use App\Http\Requests\Admin\QuestionUpdateRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Services\Admin\QuestionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{

    public function __construct(private readonly QuestionService $questionsService)
    {
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        $questions = $this->questionsService->getPagination($request->validated());


        return QuestionResource::collection($questions);
    }

    public function store(QuestionStoreRequest $request): void
    {
        $payload = $request->validated();

        $this->questionsService->store($payload);
    }

    public function show(int $id): QuestionResource
    {
        $question = $this->questionsService->getById($id);

         return new QuestionResource($question);
    }

    public function update(QuestionUpdateRequest $request, string $id): void
    {
        $this->questionsService->update($request->validated(), $id);
    }

    public function destroy(int $id): void
    {
        $this->questionsService->destroy($id);
    }
}

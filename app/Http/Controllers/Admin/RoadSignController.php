<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\QuestionStoreRequest;
use App\Http\Requests\Admin\QuestionUpdateRequest;
use App\Http\Resources\Admin\QuestionResource;
use App\Http\Resources\Admin\RoadSignResource;
use App\Services\Admin\QuestionService;
use App\Services\Admin\RoadSignService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use function Amp\Dns\query;

class RoadSignController extends Controller
{

    public function __construct(private readonly RoadSignService $roadSignService)
    {
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        $questions = $this->roadSignService->getPagination($request->validated());

         return RoadSignResource::collection($questions);
    }

    public function store(QuestionStoreRequest $request): void
    {
        $payload = $request->validated();

        $this->roadSignService->store($payload);
    }

    public function show(int $id): QuestionResource
    {
        $question = $this->roadSignService->getById($id);

         return new QuestionResource($question);
    }

    public function update(QuestionUpdateRequest $request, string $id): void
    {
        $this->roadSignService->update($request->validated(), $id);
    }

    public function destroy(int $id): void
    {
        $this->roadSignService->destroy($id);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupStoreRequest;
use App\Http\Requests\Admin\GroupUpdateRequest;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\TestStoreRequest;
use App\Http\Requests\Admin\TestUpdateRequest;
use App\Http\Resources\Admin\TestResource;
use App\Http\Resources\CategoryResource;
use App\Repositories\Admin\CategoryRepository;
use App\Services\Admin\AdminTestService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestController extends Controller
{
    public function __construct(private readonly AdminTestService $service)
    {
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        $tests = $this->service->getPagination($request->validated());

        return TestResource::collection($tests);
    }

    public function store(TestStoreRequest $request): void
    {
        $payload = $request->validated();

        $this->service->store($payload);
    }

    public function show(int $id): TestResource
    {
        $categories = $this->service->getById($id);

        return new TestResource($categories);
    }

    public function update(TestUpdateRequest $request, int $id): TestResource
    {
        $payload = $request->validated();

        $this->service->update($payload, $id);

        return $this->show($id);
    }

    public function destroy(int $id): void
    {
        $this->service->destroy($id);
    }
}

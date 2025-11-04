<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\Admin\UsersResource;
use App\Services\Admin\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        $users = $this->service->getPagination($request->validated());

        return UsersResource::collection($users);
    }

    public function store(UserStoreRequest $request): void
    {
        $this->service->store($request->validated());
    }

    public function show(int $id): UsersResource
    {
        $categories = $this->service->getById($id);

        return new UsersResource($categories);
    }

    public function update(UserUpdateRequest $request, int $id): UsersResource
    {
        $payload = $request->validated();

        $this->service->update($payload, $id);

        return $this->show($id);
    }

    public function destroy(int $id): void
    {
        $this->service->destroy($id);
    }

    public function groupUserList(ListRequest $request): AnonymousResourceCollection
    {
        $users = $this->service->groupUserList($request->validated());
        return UsersResource::collection($users);
    }

    public function results(int $id)
    {
        $user = $this->service->results($id);
        return new UsersResource($user);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GroupStoreRequest;
use App\Http\Requests\Admin\GroupUpdateRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Services\Admin\GroupService;

class GroupController extends Controller
{
    public function __construct(private readonly GroupService $groupService)
    {
    }

    public function index()
    {
        $groups = $this->groupService->getAllgroups();

        return response()->json([
            'groups' => GroupResource::collection($groups),
            'status' => 'success'
        ]);
    }

    public function store(GroupStoreRequest $request): void
    {
        $payload = $request->validated();


        $this->groupService->store($payload);
    }

    public function show(int $id): GroupResource
    {
        $categories = $this->groupService->getById($id);

        return new GroupResource($categories);
    }

    public function update(GroupUpdateRequest $request, int $id)
    {
        $payload = $request->validated();

        $this->groupService->update($payload, $id);

        return $this->show($id);
    }

    public function destroy(int $id): void
    {
        $this->groupService->destroy($id);
    }
}

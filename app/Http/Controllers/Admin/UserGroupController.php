<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListRequest;
use App\Http\Requests\Admin\UserGroupStoreRequest;
use App\Http\Requests\Admin\UserGroupUpdateRequest;
use App\Http\Resources\Admin\UserGroupResource;
use App\Services\Admin\UserGroupService;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function __construct(private readonly UserGroupService $service)
    {
    }

    public function index(ListRequest $request)
    {
       $groups = $this->service->getPagination($request->validated());


        return  UserGroupResource::collection($groups);

    }


    public function store(UserGroupStoreRequest $request)
    {
        $this->service->store($request->validated());
    }


    public function show(string $id): UserGroupResource
    {
        $group = $this->service->getById($id);

        return new UserGroupResource($group);
    }

    public function update(UserGroupUpdateRequest $request, string $id)
    {
        $this->service->update($request->validated(), $id);
    }


    public function destroy(string $id)
    {
        $this->service->destroy($id);
    }
}

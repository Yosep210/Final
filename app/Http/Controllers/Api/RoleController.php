<?php

namespace App\Http\Controllers\Api;

use App\Actions\Role\CreateRoleAction;
use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\GetRoleAction;
use App\Actions\Role\UpdateRoleAction;
use App\Data\RoleData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = GetRoleAction::run();

        return RoleResource::collection($roles)->response();
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = CreateRoleAction::run(RoleData::fromArray($request->validated()));

        return RoleResource::make($role)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Role $role): JsonResponse
    {
        return RoleResource::make($role)->response();
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = UpdateRoleAction::run($role, RoleData::fromArray($request->validated()));

        return RoleResource::make($role)->response();
    }

    public function destroy(Role $role): Response
    {
        DeleteRoleAction::run($role);

        return response()->noContent();
    }
}

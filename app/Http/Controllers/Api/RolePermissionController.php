<?php

namespace App\Http\Controllers\Api;

use App\Actions\RolePermission\CreateRolePermissionAction;
use App\Actions\RolePermission\DeleteRolePermissionAction;
use App\Actions\RolePermission\GetRolePermissionAction;
use App\Actions\RolePermission\UpdateRolePermissionAction;
use App\Data\RolePermissionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermission\StoreRolePermissionRequest;
use App\Http\Requests\RolePermission\UpdateRolePermissionRequest;
use App\Http\Resources\RolePermissionResource;
use App\Models\RolePermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RolePermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $rolePermissions = GetRolePermissionAction::run();

        return RolePermissionResource::collection($rolePermissions)->response();
    }

    public function store(StoreRolePermissionRequest $request): JsonResponse
    {
        $rolePermission = CreateRolePermissionAction::run(RolePermissionData::fromArray($request->validated()));

        return RolePermissionResource::make($rolePermission)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(int $role_id, int $permission_id): JsonResponse
    {
        $rolePermission = RolePermission::query()
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_id)
            ->firstOrFail();

        return RolePermissionResource::make($rolePermission)->response();
    }

    public function update(UpdateRolePermissionRequest $request, int $role_id, int $permission_id): JsonResponse
    {
        $rolePermission = RolePermission::query()
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_id)
            ->firstOrFail();

        $updated = UpdateRolePermissionAction::run($rolePermission, RolePermissionData::fromArray($request->validated()));

        return RolePermissionResource::make($updated)->response();
    }

    public function destroy(int $role_id, int $permission_id): Response
    {
        $rolePermission = RolePermission::query()
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_id)
            ->firstOrFail();

        DeleteRolePermissionAction::run($rolePermission);

        return response()->noContent();
    }
}

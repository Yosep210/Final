<?php

namespace App\Http\Controllers\Api;

use App\Actions\Permission\CreatePermissionAction;
use App\Actions\Permission\DeletePermissionAction;
use App\Actions\Permission\GetPermissionAction;
use App\Actions\Permission\UpdatePermissionAction;
use App\Data\PermissionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = GetPermissionAction::run();

        return PermissionResource::collection($permissions)->response();
    }

    /**
     * Store a newly created permission.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = CreatePermissionAction::run(PermissionData::fromArray($request->validated()));

        return PermissionResource::make($permission)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return PermissionResource::make($permission)
            ->response();
    }

    /**
     * Update the specified permission.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission = UpdatePermissionAction::run($permission, PermissionData::fromArray($request->validated()));

        return PermissionResource::make($permission)
            ->response();
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        DeletePermissionAction::run($permission);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

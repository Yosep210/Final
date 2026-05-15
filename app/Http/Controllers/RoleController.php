<?php

namespace App\Http\Controllers;

use App\Domain\Role\Actions\CreateRoleAction;
use App\Domain\Role\Actions\DeleteRoleAction;
use App\Domain\Role\Actions\GetRoleAction;
use App\Domain\Role\Actions\UpdateRoleAction;
use App\Domain\Role\Data\RoleData;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a paginated listing of roles.
     */
    public function index(GetRoleAction $getRoleAction): JsonResponse
    {
        $roles = $getRoleAction->execute();

        return RoleResource::collection($roles)->response();
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $createRoleAction): JsonResponse
    {
        $roleData = RoleData::fromArray($request->validated());
        $role = $createRoleAction->execute($roleData);

        return RoleResource::make($role)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        return RoleResource::make($role)->response();
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $updateRoleAction): JsonResponse
    {
        $roleData = RoleData::fromArray($request->validated());
        $updatedRole = $updateRoleAction->execute($role, $roleData);

        return RoleResource::make($updatedRole)->response();
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role, DeleteRoleAction $deleteRoleAction): JsonResponse
    {
        $deleteRoleAction->execute($role);

        return response()->json(null, 204);
    }
}

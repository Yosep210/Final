<?php

namespace App\Livewire\RolePermission;

use App\Actions\RolePermission\DeleteRolePermissionAction;
use App\Models\RolePermission;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RolePermissionTable extends PowerGridComponent
{
    public string $tableName = 'rolePermissionTable';

    public string $sortField = 'role_id';

    public string $sortDirection = 'asc';

    public function setUp(): array
    {
        return [
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return RolePermission::query()
            ->select('role_has_permissions.*', 'roles.name as role_name', 'permissions.name as permission_name')
            ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('role_id')
            ->add('role_name')
            ->add('permission_id')
            ->add('permission_name');
    }

    public function columns(): array
    {
        return [
            Column::make('Role', 'role_name')
                ->searchable()
                ->sortable(),

            Column::make('Permission', 'permission_name')
                ->searchable()
                ->sortable(),

            Column::make('Role ID', 'role_id')
                ->visible(false),

            Column::make('Permission ID', 'permission_id')
                ->visible(false),

            Column::action('Action')
                ->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('role_name')->operators(['contains']),
            Filter::inputText('permission_name')->operators(['contains']),
        ];
    }

    public function actions(RolePermission $rolePermission): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('rolepermission:edit', [
                    'roleId' => $rolePermission->role_id,
                    'permissionId' => $rolePermission->permission_id,
                ]),

            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-red-600 dark:border-pg-red-600 dark:hover:bg-pg-red-700 dark:ring-offset-pg-red-800 dark:text-pg-red-300 dark:bg-pg-red-700')
                ->confirm('Delete this role permission?')
                ->dispatch('rolepermission:delete', [
                    'roleId' => $rolePermission->role_id,
                    'permissionId' => $rolePermission->permission_id,
                ]),
        ];
    }

    #[On('rolepermission:delete')]
    public function deleteRolePermission(int $roleId, int $permissionId, DeleteRolePermissionAction $deleteRolePermissionAction): void
    {
        $rolePermission = RolePermission::query()
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->firstOrFail();

        $deleteRolePermissionAction->execute($rolePermission);

        Flux::toast(variant: 'success', text: 'Role permission deleted successfully.');
        $this->dispatch('pg:eventRefresh-rolePermissionTable');
    }
}

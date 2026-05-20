<?php

namespace App\Livewire\Role;

use App\Actions\Role\DeleteRoleAction;
use App\Models\Role;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RoleTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'roleTable';

    public string $sortField = 'name';

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
        $allowedSort = [
            'name' => 'roles.name',
            'guard_name' => 'roles.guard_name',
            'created_at' => 'roles.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'roles.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Role::query()
            ->select('roles.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('name')
            ->add('guard_name')
            ->add('created_at_formatted', fn (Role $model) => optional($model->created_at)?->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Name', 'name')->sortable(),
            Column::make('Guard Name', 'guard_name')->sortable(),
            Column::make('Created At', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('guard_name')->operators(['contains']),
        ];
    }

    public function actions(Role $role): array
    {
        return [
            Button::add('access')
                ->slot('Permissions')
                ->class(self::BUTTON_CLASS)
                ->dispatch('role:access', ['roleId' => $role->id]),

            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('role:edit', ['roleId' => $role->id]),

            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Delete this role?')
                ->dispatch('role:delete', ['roleId' => $role->id]),
        ];
    }

    #[On('role:delete')]
    public function delete(int $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        try {
            DeleteRoleAction::run($role);

            Flux::toast(variant: 'success', text: 'Role deleted successfully.');
        } catch (\Throwable $throwable) {
            Flux::toast(variant: 'error', text: $throwable->getMessage());
        }

        $this->dispatch('pg:eventRefresh-roleTable');
    }
}

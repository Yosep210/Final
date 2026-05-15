<?php

namespace App\Livewire\Role;

use App\Domain\Role\Actions\DeleteRoleAction;
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
        return Role::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('guard_name')
            ->add('created_at')
            ->add('created_at_formatted', fn(Role $model) => $model->created_at->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Guard Name', 'guard_name')
                ->searchable()
                ->sortable(),

            Column::make('Created At', 'created_at_formatted', 'created_at')
                ->searchable()
                ->sortable(),

            Column::action('Action')
                ->fixedOnResponsive(),
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
            Button::add('edit')
                ->slot('<i class="fas fa-edit"></i>')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('role:edit', ['roleId' => $role->id]),

            Button::add('delete')
                ->slot('<i class="fas fa-trash"></i>')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('delete-role', ['roleId' => $role->id]),
        ];
    }

    #[On('delete-role')]
    public function deleteRole(int $roleId, DeleteRoleAction $deleteRoleAction): void
    {
        $role = Role::query()->findOrFail($roleId);

        try {
            $deleteRoleAction->execute($role);

            Flux::toast(variant: 'success', text: 'Role deleted successfully.');
        } catch (\Exception $e) {
            Flux::toast(variant: 'error', text: $e->getMessage());
        }

        $this->dispatch('pg:eventRefresh-roleTable');
    }
}

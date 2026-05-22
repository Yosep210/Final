<?php

namespace App\Livewire\Permission;

use App\Actions\Permission\DeletePermissionAction;
use Spatie\Permission\Models\Permission;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class PermissionTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'permissionTable';

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
            'name' => 'permissions.name',
            'guard_name' => 'permissions.guard_name',
            'created_at' => 'permissions.created_at',
        ];
        $sortField = $allowedSort[$this->sortField] ?? 'permissions.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Permission::query()
            ->select('permissions.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY ' . $sortField . ' ' . $sortDirection . ') AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('name')
            ->add('guard_name')
            ->add('created_at_formatted', fn(Permission $model) => optional($model->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Name', 'name')->sortable(),
            Column::make('Guard Name', 'guard_name')->sortable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('guard_name')->operators(['contains']),
            Filter::datepicker('created_at'),
        ];
    }

    #[On('permission:delete')]
    public function delete(int $rowId): void
    {
        $permission = Permission::query()->findOrFail($rowId);

        DeletePermissionAction::run($permission);

        Flux::toast(variant: 'success', text: 'Permission deleted successfully.');

        $this->dispatch('pg:eventRefresh-permissionTable');
    }

    public function actions(Permission $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('permission:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Are you sure you want to delete this permission?')
                ->dispatch('permission:delete', ['rowId' => $row->id]),
        ];
    }
}

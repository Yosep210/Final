<?php

namespace App\Livewire\Province;

use App\Actions\Province\DeleteProvinceAction;
use App\Models\Province;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ProvinceTable extends PowerGridComponent
{
    public string $tableName = 'provinceTable';

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
        $allowedSort = ['country_id', 'name'];
        $sortField = in_array($this->sortField, $allowedSort) ? $this->sortField : 'name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Province::query()
            ->select('provincies.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY provincies.'.$sortField.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('country_id')
            ->add('name')
            ->add('code');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Country ID', 'country_id')
                ->searchable()
                ->sortable(),
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),
            Column::make('Code', 'code')
                ->searchable()
                ->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('country_id')->operators(['contains']),
            Filter::inputText('name')->operators(['contains']),
        ];
    }

    #[On('province:delete')]
    public function delete(int $rowId): void
    {
        $province = Province::query()->findOrFail($rowId);

        DeleteProvinceAction::run($province);

        Flux::toast(variant: 'success', text: 'Province deleted successfully.');

        $this->dispatch('pg:eventRefresh-provinceTable');
    }

    public function actions(Province $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('province:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->confirm('Are you sure you want to delete this province?')
                ->dispatch('province:delete', ['rowId' => $row->id]),
        ];
    }
}

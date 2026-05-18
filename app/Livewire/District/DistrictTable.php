<?php

namespace App\Livewire\District;

use App\Actions\District\DeleteDistrictAction;
use App\Models\District;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class DistrictTable extends PowerGridComponent
{
    public string $tableName = 'districtTable';

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
        $allowedSort = ['name', 'city_id', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSort) ? $this->sortField : 'name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return District::query()
            ->select('districts.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY districts.'.$sortField.' '.$sortDirection.') AS no')
            ->with('city');
    }

    public function relationSearch(): array
    {
        return [
            'city' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('city.name')
            ->add('name')
            ->add('created_at_formatted', fn (District $district) => optional($district->created_at)?->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('City', 'city.name')->searchable(),
            Column::make('Name', 'name')->searchable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')->searchable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('city.name')->operators(['contains']),
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('created_at')->operators(['contains']),
        ];
    }

    #[On('district:delete')]
    public function delete(int $rowId): void
    {
        $district = District::query()->findOrFail($rowId);

        DeleteDistrictAction::run($district);

        Flux::toast(variant: 'success', text: 'District deleted successfully.');

        $this->dispatch('pg:eventRefresh-districtTable');
    }

    public function actions(District $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('district:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('district:delete', ['rowId' => $row->id]),
        ];
    }
}

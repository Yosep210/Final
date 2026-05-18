<?php

namespace App\Livewire\City;

use App\Actions\City\DeleteCityAction;
use App\Models\City;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CityTable extends PowerGridComponent
{
    public string $tableName = 'cityTable';

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
        $allowedSort = ['name', 'province_id', 'type', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSort) ? $this->sortField : 'name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return City::query()
            ->select('cities.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY cities.' . $sortField . ' ' . $sortDirection . ') AS no');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('province_id')
            ->add('name')
            ->add('type')
            ->add('created_at_formatted', fn(City $city) => optional($city->created_at)?->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Province Id', 'province_id')->searchable(),
            Column::make('Name', 'name')->searchable(),
            Column::make('Type', 'type')->searchable(),
            Column::make('Created at', 'created_at_formatted', 'created_at'),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('province_id')->operators(['contains']),
            Filter::inputText('type')->operators(['contains']),
            Filter::datepicker('created_at'),
        ];
    }

    #[On('city:delete')]
    public function delete(int $rowId): void
    {
        $city = City::query()->findOrFail($rowId);

        DeleteCityAction::run($city);

        Flux::toast(variant: 'success', text: 'City deleted successfully.');

        $this->dispatch('pg:eventRefresh-cityTable');
    }

    public function actions(City $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('city:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->confirm('Are you sure you want to delete this city?')
                ->dispatch('city:delete', ['rowId' => $row->id]),
        ];
    }
}

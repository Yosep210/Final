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
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'cityTable';

    public string $sortField = 'provinces.name';

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
            'provinces.name' => 'provinces.name',
            'cities.name' => 'cities.name',
            'cities.type' => 'cities.type',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'provinces.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $this->sortField = $sortColumn;

        return City::query()
            ->leftJoin('provinces', 'cities.province_id', '=', 'provinces.id')
            ->select('cities.*', 'provinces.name as province_name', 'cities.name as city_name', 'cities.type as type')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortColumn.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [
            'provinces' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('province_name')
            ->add('city_name')
            ->add('type');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Province', 'province_name', 'provinces.name')->sortable(),
            Column::make('City', 'city_name', 'cities.name')->sortable(),
            Column::make('Type', 'type', 'cities.type')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('province_name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('provinces.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('city_name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('cities.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('type', 'cities.type')
                ->dataSource([
                    ['id' => 'Kota', 'name' => 'Kota'],
                    ['id' => 'Kabupaten', 'name' => 'Kabupaten'],
                ])
                ->optionValue('id')
                ->optionLabel('name'),
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
                ->class(self::BUTTON_CLASS)
                ->dispatch('city:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Are you sure you want to delete this city?')
                ->dispatch('city:delete', ['rowId' => $row->id]),
        ];
    }
}

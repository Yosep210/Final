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

    public string $sortField = 'province_name';

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
            'name' => 'cities.name',
            'province_name' => 'provincies.name',
            'type' => 'cities.type',
            'created_at' => 'cities.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'provincies.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return City::query()
            ->leftJoin('provincies', 'cities.province_id', '=', 'provincies.id')
            ->select('cities.*', 'cities.name as city_name', 'provincies.name as province_name')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [
            'province' => [
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
            ->add('type')
            ->add('created_at_formatted', fn (City $city) => optional($city->created_at)?->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Province', 'province_name')->sortable(),
            Column::make('Name', 'city_name', 'name')->sortable(),
            Column::make('Type', 'type')->sortable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')->sortable(),
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

                    return $query->where('provincies.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('cities.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('type', 'type')
                ->dataSource([
                    [
                        'id' => 'Kota',
                        'name' => 'Kota',
                    ],
                    [
                        'id' => 'Kabupaten',
                        'name' => 'Kabupaten',
                    ],
                ])
                ->optionValue('id')
                ->optionLabel('name'),
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

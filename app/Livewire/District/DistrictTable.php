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
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

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
        $allowedSort = [
            'name' => 'districts.name',
            'city_name' => 'cities.name',
            'created_at' => 'districts.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'districts.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return District::query()
            ->leftJoin('cities', 'districts.city_id', '=', 'cities.id')
            ->select('districts.*', 'cities.name as city_name')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
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
            ->add('city_name')
            ->add('name')
            ->add('created_at_formatted', fn (District $district) => optional($district->created_at)?->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('City', 'city_name')->sortable(),
            Column::make('Name', 'name')->sortable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('city_name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('cities.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('districts.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::datepicker('created_at'),
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
                ->class(self::BUTTON_CLASS)
                ->dispatch('district:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Delete this district?')
                ->dispatch('district:delete', ['rowId' => $row->id]),
        ];
    }
}

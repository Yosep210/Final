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
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'provinceTable';

    public string $sortField = 'country_name';

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
            'country_name' => 'countries.name',
            'name' => 'provinces.name',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'countries.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Province::query()
            ->leftJoin('countries', 'provinces.country_id', '=', 'countries.id')
            ->select('provinces.*', 'countries.name as country_name')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [
            'country' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('country_name')
            ->add('name');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Country', 'country_name')->sortable(),
            Column::make('Name', 'name')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('country_name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('countries.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (empty($searchTerm) || is_array($searchTerm)) {
                        return $query;
                    }

                    return $query->where('provinces.name', 'like', '%'.$searchTerm.'%');
                }),
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
                ->class(self::BUTTON_CLASS)
                ->dispatch('province:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Are you sure you want to delete this province?')
                ->dispatch('province:delete', ['rowId' => $row->id]),
        ];
    }
}

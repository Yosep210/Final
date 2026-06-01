<?php

namespace App\Livewire\Village;

use App\Actions\Village\DeleteVillageAction;
use App\Models\Village;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class VillageTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'villageTable';

    public string $sortField = 'districts.name';

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
            'districts.name' => 'districts.name',
            'villages.name' => 'villages.name',
            'postal_code_num' => 'postal_code_num',
        ];

        // Gunakan mapping untuk mendapatkan kolom database, tapi jangan menimpa $this->sortField
        $mappedColumn = $allowedSort[$this->sortField] ?? 'districts.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        // Window function butuh ekspresi asli (CAST) dan tie-breaker (id) agar deterministik
        $sortExpression = ($mappedColumn === 'postal_code_num')
            ? 'CAST(villages.postal_code AS UNSIGNED)'
            : $mappedColumn;

        return Village::query()
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->select('villages.*', 'districts.name as district_name', 'villages.name as village_name', 'villages.postal_code as postal_code')
            ->selectRaw('CAST(villages.postal_code AS UNSIGNED) as postal_code_num')
            ->selectRaw("ROW_NUMBER() OVER (ORDER BY $sortExpression $sortDirection, villages.id ASC) AS no")
            ->orderByRaw("$sortExpression $sortDirection")
            ->orderBy('villages.id', 'asc');
    }

    public function relationSearch(): array
    {
        return [
            'district' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('district_name')
            ->add('name')
            ->add('postal_code');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('District', 'district_name', 'districts.name')->sortable(),
            Column::make('Name', 'name', 'villages.name')->sortable(),
            Column::make('Postal code', 'postal_code', 'postal_code_num')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('villages.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('district_name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('districts.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('postal_code')->operators(['contains']),
        ];
    }

    #[On('village:delete')]
    public function delete(int $rowId): void
    {
        $village = Village::query()->findOrFail($rowId);

        DeleteVillageAction::run($village);

        Flux::toast(variant: 'success', text: 'Village deleted successfully.');

        $this->dispatch('pg:eventRefresh-villageTable');
    }

    public function actions(Village $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('village:edit', ['rowId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Are you sure you want to delete this village?')
                ->dispatch('village:delete', ['rowId' => $row->id]),
        ];
    }
}

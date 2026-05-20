<?php

namespace App\Livewire\Country;

use App\Actions\Country\DeleteCountryAction;
use App\Models\Country;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class CountryTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'countryTable';

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
            'iso' => 'countries.iso',
            'name' => 'countries.name',
            'nice_name' => 'countries.nice_name',
            'iso3' => 'countries.iso3',
            'numcode' => 'countries.numcode',
            'phonecode' => 'countries.phonecode',
            'status' => 'countries.status',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'countries.name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Country::query()
            ->select('countries.*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('iso')
            ->add('name')
            ->add('nice_name')
            ->add('iso3')
            ->add('numcode')
            ->add('phonecode')
            ->add('status');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('ISO', 'iso')->sortable(),
            Column::make('Name', 'name')->sortable(),
            Column::make('Nice Name', 'nice_name')->sortable(),
            Column::make('ISO3', 'iso3')->sortable(),
            Column::make('Numcode', 'numcode')->sortable(),
            Column::make('Phonecode', 'phonecode')->sortable(),
            Column::make('Status', 'status')->toggleable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('iso')->operators(['contains']),
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('nice_name')->operators(['contains']),
            Filter::inputText('iso3')->operators(['contains']),
            Filter::inputText('numcode')->operators(['contains']),
            Filter::inputText('phonecode')->operators(['contains']),
            Filter::boolean('status')->label('Active', 'Inactive'),
        ];
    }

    #[On('country:delete')]
    public function delete(int $rowId): void
    {
        $country = Country::query()->findOrFail($rowId);

        DeleteCountryAction::run($country);

        Flux::toast(variant: 'success', text: 'Country deleted successfully.');

        $this->dispatch('pg:eventRefresh-countryTable');
    }

    public function actions(Country $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('country:edit', ['countryId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Delete this country?')
                ->dispatch('country:delete', ['rowId' => $row->id]),
        ];
    }
}

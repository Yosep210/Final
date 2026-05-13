<?php

namespace App\Livewire\Country;

use App\Domain\Country\Actions\DeleteCountryAction;
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
        return Country::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('iso')
            ->add('name')
            ->add('nice_name')
            ->add('iso3')
            ->add('numcode')
            ->add('phonecode')
            ->add('status_label', fn (Country $country) => $country->status ? 'Active' : 'Inactive');
    }

    public function columns(): array
    {
        return [
            Column::make('ISO', 'iso')->sortable()->searchable(),
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Nice Name', 'nice_name')->sortable()->searchable(),
            Column::make('ISO3', 'iso3')->sortable()->searchable(),
            Column::make('Numcode', 'numcode')->sortable(),
            Column::make('Phonecode', 'phonecode')->sortable(),
            Column::make('Status', 'status_label', 'status')->sortable(),
            Column::action('Action'),
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
        ];
    }

    #[On('country:delete')]
    public function delete(DeleteCountryAction $deleteCountryAction, int $rowId): void
    {
        $country = Country::query()->findOrFail($rowId);

        $deleteCountryAction->execute($country);

        Flux::toast(variant: 'success', text: 'Country deleted successfully.');

        $this->dispatch('pg:eventRefresh-countryTable');
    }

    public function actions(Country $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('country:edit', ['countryId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-red-600 dark:border-pg-red-600 dark:hover:bg-pg-red-700 dark:ring-offset-pg-red-800 dark:text-pg-red-300 dark:bg-pg-red-700')
                ->confirm('Delete this country?')
                ->dispatch('country:delete', ['rowId' => $row->id]),
        ];
    }
}

<?php

namespace App\Livewire\Bank;

use App\Actions\Bank\DeleteBankAction;
use App\Models\Bank;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class BankTable extends PowerGridComponent
{
    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'bankTable';

    public string $sortField = 'banks.code';

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
            'banks.name' => 'banks.name',
            'banks.code' => 'banks.code',
            'banks.type' => 'banks.type',
            'banks.flipcode' => 'banks.flipcode',
            'banks.espaycode' => 'banks.espaycode',
            'banks.linkitacode' => 'banks.linkitacode',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'banks.code';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Bank::query()
            ->select('banks.*')
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
            ->add('name')
            ->add('code')
            ->add('type')
            ->add('flipcode')
            ->add('espaycode')
            ->add('linkitacode')
            ->add('logo_html', function (Bank $row) {
                $logoPath = null;

                if (! empty($row->logo)) {
                    // Normalize the path from the database
                    $logoPath = str_replace('dhaassets/backend/img/bank/', 'assets/img/bank/', $row->logo);
                    if (! str_contains($logoPath, '/')) {
                        $logoPath = 'assets/img/bank/'.$logoPath;
                    }
                } else {
                    // Fallback based on name match
                    $checkName = strtolower(str_replace(' ', '', $row->name));
                    $possibleFile = public_path("assets/img/bank/{$checkName}.png");
                    if (file_exists($possibleFile)) {
                        $logoPath = "assets/img/bank/{$checkName}.png";
                    }
                }

                if ($logoPath) {
                    $url = asset(ltrim($logoPath, '/'));

                    return '<img src="'.$url.'" alt="'.e($row->name).'" class="h-8 max-w-[80px] object-contain mx-auto" />';
                }

                return '<span class="text-zinc-400 text-xs">-</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Logo', 'logo_html'),
            Column::make('Name', 'name', 'banks.name')->sortable(),
            Column::make('Code', 'code', 'banks.code')->sortable(),
            Column::make('Type', 'type', 'banks.type')->sortable(),
            Column::make('Flip Code', 'flipcode', 'banks.flipcode')->sortable(),
            Column::make('Espay Code', 'espaycode', 'banks.espaycode')->sortable(),
            Column::make('Linkita Code', 'linkitacode', 'banks.linkitacode')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('code')->operators(['contains']),
            Filter::inputText('type')->operators(['contains']),
            Filter::inputText('flipcode')->operators(['contains']),
            Filter::inputText('espaycode')->operators(['contains']),
            Filter::inputText('linkitacode')->operators(['contains']),
        ];
    }

    #[On('bank:delete')]
    public function delete(int $rowId): void
    {
        $bank = Bank::query()->findOrFail($rowId);

        DeleteBankAction::run($bank);

        Flux::toast(variant: 'success', text: 'Bank deleted successfully.');

        $this->dispatch('pg:eventRefresh-bankTable');
    }

    public function actions(Bank $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('bank:edit', ['bankId' => $row->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Delete this bank?')
                ->dispatch('bank:delete', ['rowId' => $row->id]),
        ];
    }
}

<?php

namespace App\Livewire\Generation;

use App\Models\MemberNetwork;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class GenTable extends PowerGridComponent
{
    public string $tableName = 'genTable';

    public string $sortField = 'member_networks.generation';

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
            'member' => 'member.name',
            'username' => 'member.username',
            'sponsor' => 'sponsor.name',
            'generation' => 'member_networks.generation',
            'total_volume' => 'member_networks.total_volume',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'member_networks.generation';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return MemberNetwork::query()
            ->leftJoin('members as member', 'member_networks.member_id', '=', 'member.id')
            ->leftJoin('members as sponsor', 'member_networks.sponsored_id', '=', 'sponsor.id')
            ->leftJoin('members as parent', 'member_networks.parent_id', '=', 'parent.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'member_networks.*',
                'member.name as member_name',
                'member.username as member_username',
                'sponsor.name as sponsor_name',
                'sponsor.username as sponsor_username',
            ])
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (MemberNetwork $row) => '<div><strong>'.e(strtoupper($row->member_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->member_name).'</div>')
            ->add('sponsor', fn (MemberNetwork $row) => $row->sponsor_username ? '<div><strong>'.e(strtoupper($row->sponsor_username)).'</strong></div><div class="text-zinc-500 text-xs">'.e($row->sponsor_name).'</div>' : '-')
            ->add('generation', fn (MemberNetwork $row) => 'Gen-'.($row->generation ?? 0))
            ->add('total_volume', fn (MemberNetwork $row) => number_format((float) ($row->total_volume ?? 0), 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member')->sortable(),
            Column::make('Sponsor', 'sponsor')->sortable(),
            Column::make('Generation', 'generation')->sortable(),
            Column::make('Total Omzet', 'total_volume')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('member')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('username')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('sponsor')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('sponsor.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('generation')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member_networks.generation', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('total_volume')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member_networks.total_volume', 'like', '%'.$searchTerm.'%');
                }),
        ];
    }

    public function actions(MemberNetwork $row): array
    {
        return [
            Button::add('view')
                ->slot('View')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('generation:view', ['rowId' => $row->id]),
        ];
    }

    #[On('generation:view')]
    public function refreshTable(): void
    {
        $this->dispatch('pg:eventRefresh-genTable');
    }
}

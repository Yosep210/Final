<?php

namespace App\Livewire\Group;

use App\Models\MemberNetwork;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class GroupTable extends PowerGridComponent
{
    public string $tableName = 'groupTable';

    public string $sortField = 'member_networks.created_at';

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
            'member.username' => 'member.username',
            'member.name' => 'member.name',
            'parent.name' => 'parent.name',
            'member_networks.position' => 'member_networks.position',
            'member_networks.left_volume' => 'member_networks.left_volume',
            'member_networks.right_volume' => 'member_networks.right_volume',
            'member_networks.generation' => 'member_networks.generation',
            'member_networks.created_at' => 'member_networks.created_at',
        ];

        $mappedColumn = $allowedSort[$this->sortField] ?? 'member_networks.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return MemberNetwork::query()
            ->leftJoin('members as member', 'member_networks.member_id', '=', 'member.id')
            ->leftJoin('members as parent', 'member_networks.parent_id', '=', 'parent.id')
            ->whereDoesntHave('member.roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'member_networks.*',
                'member.name as member_name',
                'member.username as member_username',
                'parent.name as parent_name',
                'parent.username as parent_username',
            ])
            ->whereNotNull('member_networks.parent_id')
            ->selectRaw("ROW_NUMBER() OVER (ORDER BY $mappedColumn $sortDirection, member_networks.id ASC) AS no")
            ->orderByRaw("$mappedColumn $sortDirection")
            ->orderBy('member_networks.id', 'asc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username', fn (MemberNetwork $row) => $row->member_username)
            ->add('member', fn (MemberNetwork $row) => $row->member_name)
            ->add('parent', fn (MemberNetwork $row) => $row->parent_name ?? '-')
            ->add('position', fn (MemberNetwork $row) => ucfirst($row->position ?? '-'))
            ->add('left_volume', fn (MemberNetwork $row) => number_format((float) ($row->left_volume ?? 0), 2))
            ->add('right_volume', fn (MemberNetwork $row) => number_format((float) ($row->right_volume ?? 0), 2))
            ->add('generation', fn (MemberNetwork $row) => (string) ($row->generation ?? 0))
            ->add('created_at_formatted', fn (MemberNetwork $row) => optional($row->created_at)->format('Y M d H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'member.username')->sortable(),
            Column::make('Member', 'member', 'member.name')->sortable(),
            Column::make('Parent', 'parent', 'parent.name')->sortable(),
            Column::make('Position', 'position', 'member_networks.position')->sortable(),
            Column::make('Left Volume', 'left_volume', 'member_networks.left_volume')->sortable(),
            Column::make('Right Volume', 'right_volume', 'member_networks.right_volume')->sortable(),
            Column::make('Generation', 'generation', 'member_networks.generation')->sortable(),
            Column::make('Created At', 'created_at_formatted', 'member_networks.created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('username')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('member')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('parent')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('parent.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('position', 'member_networks.position')
                ->dataSource(collect([
                    ['id' => 'left', 'name' => 'Left'],
                    ['id' => 'right', 'name' => 'Right'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::inputText('left_volume')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member_networks.left_volume', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('right_volume')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('member_networks.right_volume', 'like', '%'.$searchTerm.'%');
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
            Filter::datepicker('created_at_formatted', 'member_networks.created_at'),
        ];
    }

    public function actions(MemberNetwork $row): array
    {
        return [
            Button::add('view')
                ->slot('View')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('group:view', ['rowId' => $row->id]),
        ];
    }

    #[On('group:view')]
    public function refreshTable(): void
    {
        $this->dispatch('pg:eventRefresh-groupTable');
    }
}

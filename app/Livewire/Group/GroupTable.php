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

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

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
            'parent' => 'parent.name',
            'position' => 'member_networks.position',
            'left_volume' => 'member_networks.left_volume',
            'right_volume' => 'member_networks.right_volume',
            'generation' => 'member_networks.generation',
            'created_at' => 'member_networks.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'member_networks.created_at';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return MemberNetwork::query()
            ->leftJoin('members as member', 'member_networks.member_id', '=', 'member.id')
            ->leftJoin('members as parent', 'member_networks.parent_id', '=', 'parent.id')
            ->select([
                'member_networks.*',
                'member.name as member_name',
                'member.username as member_username',
                'parent.name as parent_name',
                'parent.username as parent_username',
            ])
            ->whereNotNull('member_networks.parent_id')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('member', fn (MemberNetwork $row) => $row->member_name)
            ->add('username', fn (MemberNetwork $row) => $row->member_username)
            ->add('parent', fn (MemberNetwork $row) => $row->parent_name ?? '-')
            ->add('position', fn (MemberNetwork $row) => ucfirst($row->position ?? '-'))
            ->add('left_volume', fn (MemberNetwork $row) => number_format((float) ($row->left_volume ?? 0), 2))
            ->add('right_volume', fn (MemberNetwork $row) => number_format((float) ($row->right_volume ?? 0), 2))
            ->add('generation', fn (MemberNetwork $row) => (string) ($row->generation ?? 0));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Member', 'member')->sortable(),
            Column::make('Username', 'username')->sortable(),
            Column::make('Parent', 'parent')->sortable(),
            Column::make('Position', 'position')->sortable(),
            Column::make('Left Volume', 'left_volume')->sortable(),
            Column::make('Right Volume', 'right_volume')->sortable(),
            Column::make('Generation', 'generation')->sortable(),
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
            Filter::inputText('parent')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('parent.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('position')
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

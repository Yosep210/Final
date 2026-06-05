<?php

namespace App\Livewire\Sponsor;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class SponsorTable extends PowerGridComponent
{
    public string $tableName = 'sponsorTable';

    public string $sortField = 'total_sponsored';

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
            'members.name' => 'members.name',
            'members.username' => 'members.username',
            'sponsor_rank' => 'sponsor_net.current_rank',
            'members.status' => 'members.status',
            'members.created_at' => 'members.created_at',
            'member_active' => 'member_active',
            'member_non_active' => 'member_non_active',
            'total_sponsored' => 'total_sponsored',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'total_sponsored';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        $this->sortField = $sortColumn;

        $windowOrderMap = [
            'total_sponsored' => '(select count(*) from member_networks as child_network where child_network.sponsored_id = members.id)',
            'member_active' => "(select count(*) from member_networks as child_network inner join members as child_member on child_member.id = child_network.member_id where child_network.sponsored_id = members.id and child_member.status = 'active')",
            'member_non_active' => "(select count(*) from member_networks as child_network inner join members as child_member on child_member.id = child_network.member_id where child_network.sponsored_id = members.id and child_member.status != 'active')",
        ];

        $windowOrder = $windowOrderMap[$sortColumn] ?? $sortColumn;

        return Member::query()
            ->leftJoin('member_networks as sponsor_net', 'sponsor_net.member_id', '=', 'members.id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('member_networks')
                    ->whereColumn('sponsored_id', 'members.id');
            })
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
            ->select([
                'members.id',
                'members.username',
                'members.name',
                'members.status',
                'members.created_at',
                'sponsor_net.current_rank as sponsor_rank',
            ])
            ->selectSub(function ($query) {
                $query->from('member_networks as child_network')
                    ->join('members as child_member', 'child_member.id', '=', 'child_network.member_id')
                    ->selectRaw('count(*)')
                    ->whereColumn('child_network.sponsored_id', 'members.id')
                    ->where('child_member.status', 'active');
            }, 'member_active')
            ->selectSub(function ($query) {
                $query->from('member_networks as child_network')
                    ->join('members as child_member', 'child_member.id', '=', 'child_network.member_id')
                    ->selectRaw('count(*)')
                    ->whereColumn('child_network.sponsored_id', 'members.id')
                    ->where('child_member.status', '!=', 'active');
            }, 'member_non_active')
            ->selectSub(function ($query) {
                $query->from('member_networks as child_network')
                    ->selectRaw('count(*)')
                    ->whereColumn('child_network.sponsored_id', 'members.id');
            }, 'total_sponsored')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$windowOrder.' '.$sortDirection.') AS no')
            ->orderBy($sortColumn, $sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username')
            ->add('name')
            ->add(
                'sponsor_rank',
                function (Member $member) {
                    $rank = strtolower($member->sponsor_rank ?: 'member');
                    $class = match ($rank) {
                        'member' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                        'star' => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                        default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                    };

                    return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($rank).'</span>';
                }
            )
            ->add('member_active', fn (Member $member) => (string) ($member->member_active ?? 0))
            ->add('member_non_active', fn (Member $member) => (string) ($member->member_non_active ?? 0))
            ->add(
                'status',
                function (Member $member) {
                    $status = strtolower($member->status ?: 'active');
                    $class = match ($status) {
                        'active' => 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20',
                        'suspended' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20',
                        'inactive' => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20',
                        default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                    };

                    return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($status).'</span>';
                }
            )
            ->add('join_date_formatted', fn (Member $member) => $member->created_at?->locale('id')?->isoFormat('DD MMM YY HH:mm'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username', 'members.username')->sortable(),
            Column::make('Nama', 'name', 'members.name')->sortable(),
            Column::make('Rank', 'sponsor_rank', 'sponsor_net.current_rank')->sortable(),
            Column::make('Total Active', 'member_active', 'member_active')->sortable(),
            Column::make('Total Non-Active', 'member_non_active', 'member_non_active')->sortable(),
            Column::make('Status', 'status', 'members.status')->sortable(),
            Column::make('Date Actived', 'join_date_formatted', 'members.created_at')->sortable(),
            Column::action('Action'),
        ];
    }

    public function actions(Member $row): array
    {
        return [
            Button::add('view-gen')
                ->slot('Gen')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('sponsor:view-gen', ['username' => $row->username]),
        ];
    }

    #[On('sponsor:view-gen')]
    public function viewGen(string $username): void
    {
        $this->redirect(route('network.index', ['username' => $username]), navigate: true);
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

                    return $query->where('members.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('username')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('members.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('sponsor_rank', 'sponsor_net.current_rank')
                ->dataSource(collect([
                    ['id' => 'star', 'name' => 'Star'],
                    ['id' => 'member', 'name' => 'Member'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::inputText('member_active')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->whereRaw('(select count(*) from member_networks as child_network inner join members as child_member on child_member.id = child_network.member_id where child_network.sponsored_id = members.id and child_member.status = ?) like ?', ['active', '%'.$searchTerm.'%']);
                }),
            Filter::inputText('member_non_active')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->whereRaw('(select count(*) from member_networks as child_network inner join members as child_member on child_member.id = child_network.member_id where child_network.sponsored_id = members.id and child_member.status != ?) like ?', ['active', '%'.$searchTerm.'%']);
                }),
            Filter::select('status', 'members.status')
                ->dataSource(collect([
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'suspended', 'name' => 'Suspended'],
                    ['id' => 'inactive', 'name' => 'Inactive'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::datepicker('join_date', 'members.created_at'),
        ];
    }
}

<?php

namespace App\Livewire\Sponsor;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class SponsorTable extends PowerGridComponent
{
    public string $tableName = 'sponsorTable';

    public string $sortField = 'username';

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
            'username' => 'members.username',
            'name' => 'members.name',
            'current_rank' => 'network.current_rank',
            'member_active' => 'member_active',
            'member_non_active' => 'member_non_active',
            'status' => 'members.status',
            'join_date' => 'members.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'members.username';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return Member::query()
            ->join('member_networks as network', 'network.sponsored_id', '=', 'members.id')
            ->select([
                'members.*',
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
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY '.$sortField.' '.$sortDirection.') AS no');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username')
            ->add('name')
            ->add('current_rank', fn (Member $member) => $member->network->current_rank ?? 'member')
            ->add('member_active', fn (Member $member) => (string) ($member->member_active ?? 0))
            ->add('member_non_active', fn (Member $member) => (string) ($member->member_non_active ?? 0))
            ->add('status')
            ->add('join_date_formatted', fn (Member $member) => optional($member->created_at)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username')->sortable(),
            Column::make('Name', 'name')->sortable(),
            Column::make('Rank', 'current_rank')->sortable(),
            Column::make('Member Active', 'member_active')->sortable(),
            Column::make('Member Non Active', 'member_non_active')->sortable(),
            Column::make('Status', 'status')->sortable(),
            Column::make('Join Date', 'join_date_formatted', 'created_at')->sortable(),
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
            Filter::inputText('rank')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('network.current_rank', 'like', '%'.$searchTerm.'%');
                }),
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

<?php

namespace App\Livewire\Member;

use App\Actions\Member\DeleteMemberAction;
use App\Models\Member;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MemberTable extends PowerGridComponent
{
    use AuthorizesRequests;

    private const BUTTON_CLASS = 'pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700';

    public string $tableName = 'memberTable';

    public string $sortField = 'username';

    public string $sortDirection = 'asc';

    public bool $canManageMembers = false;

    public function setUp(): array
    {
        $this->authorize('viewAny', Member::class);
        $this->canManageMembers = auth()->user()?->can('create', Member::class) ?? false;

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
            'rank' => 'network.rank',
            'contact' => 'members.email',
            'sponsor' => 'sponsor_member.name',
            'parent' => 'parent_member.name',
            'position' => 'network.position',
            'status' => 'members.status',
            'datecreated' => 'members.created_at',
            'last_login' => 'members.last_login_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'members.username';

        $sortDirection = $this->sortDirection === 'desc'
            ? 'desc'
            : 'asc';

        return Member::query()
            ->leftJoin(
                'member_networks as network',
                'network.member_id',
                '=',
                'members.id'
            )

            ->leftJoin(
                'member_profile as profile',
                'profile.member_id',
                '=',
                'members.id'
            )

            ->leftJoin(
                'members as sponsor_member',
                'network.sponsored_id',
                '=',
                'sponsor_member.id'
            )

            ->leftJoin(
                'members as parent_member',
                'network.parent_id',
                '=',
                'parent_member.id'
            )

            ->select([
                'members.*',

                'network.rank as network_rank',
                'network.position as network_position',

                'profile.phone as profile_phone',

                'sponsor_member.name as sponsor_name',
                'parent_member.name as parent_name',
            ])

            ->selectRaw("
            ROW_NUMBER() OVER (
                ORDER BY {$sortField} {$sortDirection}
            ) AS no
        ");
    }

    public function relationSearch(): array
    {
        return [
            'profile' => [
                'phone',
            ],
            'network' => [
                'sponsored_id',
                'parent_id',
                'position',
                'rank',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('no')
            ->add('username')

            ->add('name')

            ->add(
                'rank',
                fn (Member $member) => $member->network_rank
            )

            ->add('contact', function (Member $member) {
                $email = $member->email ?: '-';
                $phone = $member->profile_phone ?: '-';

                return $email.' / '.$phone;
            })

            ->add(
                'sponsor',
                fn (Member $member) => $member->sponsor_name
            )

            ->add(
                'parent',
                fn (Member $member) => $member->parent_name
            )

            ->add(
                'position',
                fn (Member $member) => $member->network_position
            )

            ->add('status')

            ->add(
                'datecreated_formatted',
                fn (Member $member) => optional($member->created_at)
                    ->format('d M Y H:i')
            )

            ->add(
                'last_login_formatted',
                fn (Member $member) => optional($member->last_login_at)
                    ->format('d M Y H:i')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Username', 'username')->sortable(),
            Column::make('Name', 'name')->sortable(),
            Column::make('Rank', 'rank')->sortable(),
            Column::make('Contact', 'contact')->sortable(),
            Column::make('Sponsor', 'sponsor')->sortable(),
            Column::make('Upline', 'parent')->sortable(),
            Column::make('Position', 'position')->sortable(),
            Column::make('Status', 'status')->sortable(),
            Column::make('Datecreated', 'datecreated_formatted', 'created_at')->sortable(),
            Column::make('Last Login', 'last_login_formatted', 'last_login_at')->sortable(),
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

                    return $query->where('network.rank', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('email')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('members.email', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('phone')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('profile.phone', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('sponsor')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('sponsor_member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('parent')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('parent_member.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('position')
                ->dataSource(collect([
                    ['id' => 'Left', 'name' => 'Left'],
                    ['id' => 'Right', 'name' => 'Right'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::select('status', 'members.status')
                ->dataSource(collect([
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'suspended', 'name' => 'Suspended'],
                    ['id' => 'inactive', 'name' => 'Inactive'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::datepicker('created_at', 'members.created_at'),
        ];
    }

    public function actions(Member $member): array
    {
        if (! $this->canManageMembers) {
            return [];
        }

        return [
            Button::add('edit')
                ->slot('Edit')
                ->class(self::BUTTON_CLASS)
                ->dispatch('member:edit', ['memberId' => $member->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class(self::BUTTON_CLASS)
                ->confirm('Delete this member?')
                ->dispatch('member:delete', ['memberId' => $member->id]),
        ];
    }

    #[On('member:delete')]
    public function delete(int $memberId): void
    {
        $member = Member::query()->findOrFail($memberId);
        $this->authorize('Delete', $member);

        try {
            DeleteMemberAction::run($member);
            Flux::toast(variant: 'success', text: 'Member deleted successfully.');
        } catch (\Throwable $throwable) {
            Flux::toast(variant: 'error', text: $throwable->getMessage());
        }

        $this->dispatch('pg:eventRefresh-memberTable');
    }
}

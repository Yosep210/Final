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

    public string $sortField = 'name';

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
            'name' => 'members.name',
            'username' => 'members.username',
            'rank' => 'network.rank',
            'email' => 'members.email',
            'phone' => 'profile.phone',
            'sponsor' => 'sponsor_member.name',
            'parent' => 'parent_member.name',
            'position' => 'network.position',
            'status' => 'members.status',
            'created_at' => 'members.created_at',
        ];

        $sortField = $allowedSort[$this->sortField] ?? 'members.name';

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

            ->add('name')

            ->add('username')

            ->add(
                'rank',
                fn (Member $member) => $member->network_rank
            )

            ->add('email')

            ->add(
                'phone',
                fn (Member $member) => $member->profile_phone
            )

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
                'created_at_formatted',
                fn (Member $member) => optional($member->created_at)
                    ->format('d M Y H:i')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('Name', 'name')->sortable(),
            Column::make('Username', 'username')->sortable(),
            Column::make('Rank', 'rank')->sortable(),
            Column::make('Email', 'email')->sortable(),
            Column::make('Phone', 'phone')->sortable(),
            Column::make('Sponsor', 'sponsor')->sortable(),
            Column::make('Parent', 'parent')->sortable(),
            Column::make('Position', 'position')->sortable(),
            Column::make('Status', 'status')->sortable(),
            Column::make('Created at', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action')->fixedOnResponsive(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('username')->operators(['contains']),
            Filter::inputText('rank')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
            Filter::inputText('phone')->operators(['contains']),
            Filter::inputText('sponsor')->operators(['contains']),
            Filter::inputText('parent')->operators(['contains']),
            Filter::select('position')
                ->dataSource(collect([
                    ['id' => 'Left', 'name' => 'Left'],
                    ['id' => 'Right', 'name' => 'Right'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::select('status', 'status')
                ->dataSource(collect([
                    ['id' => 'active', 'name' => 'Active'],
                    ['id' => 'suspended', 'name' => 'Suspended'],
                    ['id' => 'inactive', 'name' => 'Inactive'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::datepicker('created_at'),
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

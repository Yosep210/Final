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

    public string $sortField = 'members.created_at';

    public string $sortDirection = 'desc';

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
            'members.name' => 'members.name',
            'members.username' => 'members.username',
            'network.current_rank' => 'network.current_rank',
            'members.email' => 'members.email',
            'sponsor_member.name' => 'sponsor_member.name',
            'parent_member.name' => 'parent_member.name',
            'network.position' => 'network.position',
            'members.status' => 'members.status',
            'members.created_at' => 'members.created_at',
            'members.last_login_at' => 'members.last_login_at',
        ];

        $sortColumn = $allowedSort[$this->sortField] ?? 'members.created_at';

        $sortDirection = $this->sortDirection === 'desc'
            ? 'desc'
            : 'asc';

        $this->sortField = $sortColumn;

        return Member::query()
            ->leftJoin(
                'member_networks as network',
                'network.member_id',
                '=',
                'members.id'
            )

            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Staff']);
            })
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

                'network.current_rank as network_rank',
                'network.position as network_position',

                'profile.phone as profile_phone',

                'sponsor_member.name as sponsor_name',
                'sponsor_member.username as sponsor_username',
                'parent_member.name as parent_name',
                'parent_member.username as parent_username',
            ])

            ->selectRaw("
            ROW_NUMBER() OVER (
                ORDER BY $sortColumn $sortDirection
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
                'current_rank',
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
                function (Member $member) {
                    $rank = strtolower($member->network_rank ?: 'member');
                    $class = match ($rank) {
                        'member' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20',
                        'star' => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20',
                        default => 'bg-zinc-50 text-zinc-700 ring-zinc-600/10 dark:bg-zinc-500/10 dark:text-zinc-400 dark:ring-zinc-500/20',
                    };

                    return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset '.$class.'">'.ucfirst($rank).'</span>';
                }
            )
            ->add('contact', function (Member $member) {
                $email = $member->email ?: '-';
                $phone = $member->profile_phone ?: '-';

                return '<div>'.e($email).'</div><div class="text-zinc-500 text-xs">'.e($phone).'</div>';
            })
            ->add('sponsor', fn (Member $member) => '<div><strong>'.e(strtoupper($member->sponsor_username ?? '')).'</strong></div><div class="text-zinc-500 text-xs">'.e($member->sponsor_name).'</div>')
            ->add('parent', fn (Member $member) => '<div><strong>'.e(strtoupper($member->parent_username ?? '')).'</strong></div><div class="text-zinc-500 text-xs">'.e($member->parent_name).'</div>')
            ->add(
                'position',
                fn (Member $member) => ucfirst($member->network_position ?: 'left')
            )
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
            ->add(
                'created_at_formatted',
                fn (Member $member) => $member->created_at?->locale('id')?->isoFormat('DD MMM YY HH:mm'),
            )
            ->add(
                'last_login_at_formatted',
                fn (Member $member) => $member->last_login_at?->locale('id')?->isoFormat('DD MMM YY HH:mm')
            );
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'no'),
            Column::make('USERNAME', 'username', 'members.username')->sortable(),
            Column::make('NAMA', 'name', 'members.name')->sortable(),
            Column::make('RANK', 'rank', 'network.current_rank')->sortable(),
            Column::make('KONTAK', 'contact'),
            Column::make('SPONSOR', 'sponsor', 'sponsor_member.name')->sortable(),
            Column::make('UPLINE', 'parent', 'parent_member.name')->sortable(),
            Column::make('POSISI', 'position', 'network.position')->sortable(),
            Column::make('STATUS', 'status', 'members.status')->sortable(),
            Column::make('TANGGAL DAFTAR', 'created_at_formatted', 'members.created_at')->sortable(),
            Column::make('TERAKHIR LOGIN', 'last_login_at_formatted', 'members.last_login_at')->sortable(),
            Column::action('PROSES')->fixedOnResponsive(),
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

                    return $query->where('members.username', 'like', '%'.$searchTerm.'%');
                }),
            Filter::inputText('name')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where('members.name', 'like', '%'.$searchTerm.'%');
                }),
            Filter::select('rank', 'network.current_rank')
                ->dataSource(collect([
                    ['id' => 'star', 'name' => 'Star'],
                    ['id' => 'member', 'name' => 'Member'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
            Filter::inputText('contact')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;
                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('members.email', 'like', '%'.$searchTerm.'%')
                            ->orWhere('profile.phone', 'like', '%'.$searchTerm.'%');
                    });
                }),
            Filter::inputText('sponsor')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('sponsor_member.name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('sponsor_member.username', 'like', '%'.$searchTerm.'%');
                    });
                }),
            Filter::inputText('parent')
                ->operators(['contains'])
                ->builder(function (Builder $query, $value) {
                    $searchTerm = is_array($value) ? ($value['value'] ?? '') : $value;

                    if (is_array($searchTerm) || empty($searchTerm)) {
                        return $query;
                    }

                    return $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('parent_member.name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('parent_member.username', 'like', '%'.$searchTerm.'%');
                    });
                }),
            Filter::select('position', 'network.position')
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
            Filter::datepicker('created_at_formatted', 'members.created_at'),
            Filter::datepicker('last_login_at_formatted', 'members.last_login_at'),
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

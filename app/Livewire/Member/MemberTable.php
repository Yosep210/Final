<?php

namespace App\Livewire\Member;

use App\Domain\Member\Actions\DeleteMemberAction;
use App\Models\Member;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class MemberTable extends PowerGridComponent
{
    public string $tableName = 'memberTable';

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
        return Member::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('username')
            ->add('email')
            ->add('status')
            ->add('created_at_formatted', fn (Member $member) => optional($member->created_at)?->format('d/m/Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Username', 'username')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Status', 'status')->sortable()->searchable(),
            Column::make('Created At', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::inputText('name')->operators(['contains']),
            Filter::inputText('username')->operators(['contains']),
            Filter::inputText('email')->operators(['contains']),
            Filter::select('status', 'status')
                ->dataSource(collect([
                    ['id' => 'active', 'name' => 'active'],
                    ['id' => 'suspended', 'name' => 'suspended'],
                    ['id' => 'inactive', 'name' => 'inactive'],
                ]))
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }

    public function actions(Member $member): array
    {
        return [
            Button::add('edit')
                ->slot('Edit')
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('member:edit', ['memberId' => $member->id]),
            Button::add('delete')
                ->slot('Delete')
                ->class('pg-btn-white dark:ring-pg-red-600 dark:border-pg-red-600 dark:hover:bg-pg-red-700 dark:ring-offset-pg-red-800 dark:text-pg-red-300 dark:bg-pg-red-700')
                ->confirm('Delete this member?')
                ->dispatch('member:delete', ['memberId' => $member->id]),
        ];
    }

    #[On('member:delete')]
    public function deleteMember(int $memberId, DeleteMemberAction $deleteMemberAction): void
    {
        $member = Member::query()->findOrFail($memberId);

        try {
            $deleteMemberAction->execute($member);
            Flux::toast(variant: 'success', text: 'Member deleted successfully.');
        } catch (\Throwable $throwable) {
            Flux::toast(variant: 'error', text: $throwable->getMessage());
        }

        $this->dispatch('pg:eventRefresh-memberTable');
    }
}

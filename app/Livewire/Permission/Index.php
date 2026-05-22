<?php

namespace App\Livewire\Permission;

use App\Actions\Permission\CreatePermissionAction;
use App\Actions\Permission\UpdatePermissionAction;
use App\Data\PermissionData;
use App\Http\Requests\Permission\StorePermissionRequest;
use Spatie\Permission\Models\Permission;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Permission')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingPermissionId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingPermissionId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('permission:edit')]
    public function edit(int $rowId): void
    {
        $permission = Permission::query()->findOrFail($rowId);

        $this->editingPermissionId = $permission->id;
        $this->form = [
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $permission = $this->editingPermissionId ? Permission::query()->findOrFail($this->editingPermissionId) : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($permission)],
            [],
            $this->prefixedAttributes(),
        );

        $permissionData = PermissionData::fromArray($validated['form']);

        if ($permission) {
            UpdatePermissionAction::run($permission, $permissionData);

            Flux::toast(variant: 'success', text: 'Permission updated successfully.');
        } else {
            CreatePermissionAction::run($permissionData);

            Flux::toast(variant: 'success', text: 'Permission created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-permissionTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingPermissionId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.permission.index')
            ->layout('layouts.app', ['title' => __('Permission')]);
    }

    protected function prefixedRules(?Permission $permission = null): array
    {
        $rules = StorePermissionRequest::permissionRules($permission);

        return collect($rules)
            ->mapWithKeys(fn(array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    protected function prefixedAttributes(): array
    {
        return collect(StorePermissionRequest::attributeLabels())
            ->mapWithKeys(fn(string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => null,
            'guard_name' => null,
        ];
    }
}

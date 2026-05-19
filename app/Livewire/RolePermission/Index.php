<?php

namespace App\Livewire\RolePermission;

use App\Actions\RolePermission\CreateRolePermissionAction;
use App\Actions\RolePermission\UpdateRolePermissionAction;
use App\Data\RolePermissionData;
use App\Http\Requests\RolePermission\StoreRolePermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Role Permission')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingRoleId = null;

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
        $this->editingRoleId = null;
        $this->editingPermissionId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('rolepermission:edit')]
    public function edit(int $roleId, int $permissionId): void
    {
        $rolePermission = RolePermission::query()
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->firstOrFail();

        $this->editingRoleId = $rolePermission->role_id;
        $this->editingPermissionId = $rolePermission->permission_id;
        $this->form = [
            'role_id' => $rolePermission->role_id,
            'permission_id' => $rolePermission->permission_id,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(CreateRolePermissionAction $createRolePermissionAction, UpdateRolePermissionAction $updateRolePermissionAction): void
    {
        $rolePermission = $this->getEditingRow();

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($rolePermission)],
            [],
            $this->prefixedAttributes(),
        );

        $rolePermissionData = RolePermissionData::fromArray($validated['form']);

        if ($rolePermission) {
            $updateRolePermissionAction->execute($rolePermission, $rolePermissionData);

            Flux::toast(variant: 'success', text: 'Role permission updated successfully.');
        } else {
            $createRolePermissionAction->execute($rolePermissionData);

            Flux::toast(variant: 'success', text: 'Role permission created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-rolePermissionTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingRoleId = null;
        $this->editingPermissionId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    private function getEditingRow(): ?RolePermission
    {
        if (! $this->editingRoleId || ! $this->editingPermissionId) {
            return null;
        }

        return RolePermission::query()
            ->where('role_id', $this->editingRoleId)
            ->where('permission_id', $this->editingPermissionId)
            ->first();
    }

    private function prefixedRules(?RolePermission $rolePermission = null): array
    {
        $rules = StoreRolePermissionRequest::rolePermissionRules($this->form, $rolePermission);

        $prefixed = [];
        foreach ($rules as $key => $rule) {
            $prefixed["form.{$key}"] = $rule;
        }

        return $prefixed;
    }

    private function prefixedAttributes(): array
    {
        return collect(StoreRolePermissionRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    public function render()
    {
        return view('livewire.rolepermission.index', [
            'roles' => Role::query()->orderBy('name')->pluck('name', 'id'),
            'permissions' => Permission::query()->orderBy('name')->pluck('name', 'id'),
        ])
            ->layout('layouts.app', ['title' => __('Role Permission')]);
    }
}

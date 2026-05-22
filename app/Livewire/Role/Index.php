<?php

namespace App\Livewire\Role;

use App\Actions\Role\CreateRoleAction;
use App\Actions\Role\UpdateRoleAction;
use App\Data\RoleData;
use App\Http\Requests\Role\StoreRoleRequest;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Title('Role')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingRoleId = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public bool $showPermissionModal = false;

    public ?int $permissionRoleId = null;

    public string $permissionRoleName = '';

    /**
     * @var array<int, bool>
     */
    public array $permissionAccess = [];

    /**
     * @var array<int, string>
     */
    public array $permissionOptions = [];

    public function mount(): void
    {
        $this->resetForm();
        $this->resetPermissionForm();
    }

    public function create(): void
    {
        $this->editingRoleId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('role:edit')]
    public function edit(int $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        $this->editingRoleId = $role->id;
        $this->form = [
            'name' => $role->name,
            'guard_name' => $role->guard_name,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $role = $this->editingRoleId
            ? Role::query()->findOrFail($this->editingRoleId)
            : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($role)],
            [],
            $this->prefixedAttributes(),
        );

        $roleData = RoleData::fromArray($validated['form']);

        if ($role) {
            UpdateRoleAction::run($role, $roleData);

            Flux::toast(variant: 'success', text: 'Role updated successfully.');
        } else {
            CreateRoleAction::run($roleData);

            Flux::toast(variant: 'success', text: 'Role created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-roleTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingRoleId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('role:access')]
    public function access(int $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        $this->permissionRoleId = $role->id;
        $this->permissionRoleName = $role->name;
        $this->permissionOptions = Permission::query()->orderBy('name')->pluck('name', 'id')->toArray();

        $assignedPermissions = $role->permissions()->pluck('id')->toArray();

        $this->permissionAccess = array_fill_keys(array_keys($this->permissionOptions), false);

        foreach ($assignedPermissions as $permissionId) {
            if (array_key_exists($permissionId, $this->permissionAccess)) {
                $this->permissionAccess[$permissionId] = true;
            }
        }

        $this->resetValidation();
        $this->showPermissionModal = true;
    }

    public function saveRolePermissions(): void
    {
        $this->validate([
            'permissionAccess' => ['array'],
            'permissionAccess.*' => ['boolean'],
        ]);

        if ($this->permissionRoleId === null) {
            return;
        }

        $selectedPermissionIds = array_keys(array_filter($this->permissionAccess));

        $role = Role::query()->findOrFail($this->permissionRoleId);

        // The Spatie package handles the diffing and database updates automatically
        $role->syncPermissions($selectedPermissionIds);

        Flux::toast(variant: 'success', text: 'Role permissions updated successfully.');
        $this->closePermissionModal();
        $this->dispatch('pg:eventRefresh-roleTable');
    }

    public function closePermissionModal(): void
    {
        $this->showPermissionModal = false;
        $this->resetPermissionForm();
        $this->resetValidation();
    }

    private function resetPermissionForm(): void
    {
        $this->permissionRoleId = null;
        $this->permissionRoleName = '';
        $this->permissionAccess = [];
        $this->permissionOptions = [];
    }

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'guard_name' => 'web',
        ];
    }

    private function prefixedRules(?Role $role = null): array
    {
        $rules = StoreRoleRequest::roleRules($role);

        $prefixed = [];
        foreach ($rules as $key => $rule) {
            $prefixed["form.{$key}"] = $rule;
        }

        return $prefixed;
    }

    private function prefixedAttributes(): array
    {
        $attributes = StoreRoleRequest::attributeLabels();

        $prefixed = [];
        foreach ($attributes as $key => $attribute) {
            $prefixed["form.{$key}"] = $attribute;
        }

        return $prefixed;
    }

    public function render()
    {
        return view('livewire.role.index')
            ->layout('layouts.app', ['title' => __('Role')]);
    }
}

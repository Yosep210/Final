<?php

namespace App\Livewire\Role;

use App\Domain\Role\Actions\CreateRoleAction;
use App\Domain\Role\Actions\UpdateRoleAction;
use App\Domain\Role\Data\RoleData;
use App\Domain\Role\Support\RoleValidation;
use App\Models\Role;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Role')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingRoleId = null;

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

    public function save(CreateRoleAction $createRoleAction, UpdateRoleAction $updateRoleAction): void
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
            $updateRoleAction->execute($role, $roleData);

            Flux::toast(variant: 'success', text: 'Role updated successfully.');
        } else {
            $createRoleAction->execute($roleData);

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

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'guard_name' => 'web',
        ];
    }

    private function prefixedRules(?Role $role = null): array
    {
        $rules = RoleValidation::rules($role);

        $prefixed = [];
        foreach ($rules as $key => $rule) {
            $prefixed["form.{$key}"] = $rule;
        }

        return $prefixed;
    }

    private function prefixedAttributes(): array
    {
        $attributes = RoleValidation::attributes();

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

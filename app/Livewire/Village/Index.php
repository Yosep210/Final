<?php

namespace App\Livewire\Village;

use App\Actions\Village\CreateVillageAction;
use App\Actions\Village\UpdateVillageAction;
use App\Data\VillageData;
use App\Http\Requests\Village\StoreVillageRequest;
use App\Models\District;
use App\Models\Village;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Village')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingVillageId = null;

    public array $districts = [];

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->districts = District::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingVillageId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('village:edit')]
    public function edit(int $rowId): void
    {
        $village = Village::query()->findOrFail($rowId);

        $this->editingVillageId = $village->id;
        $this->form = [
            'district_id' => $village->district_id,
            'name' => $village->name,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $village = $this->editingVillageId
            ? Village::query()->findOrFail($this->editingVillageId)
            : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($village)],
            [],
            $this->prefixedAttributes(),
        );

        $villageData = VillageData::fromArray($validated['form']);

        if ($village) {
            UpdateVillageAction::run($village, $villageData);

            Flux::toast(variant: 'success', text: 'Village updated successfully.');
        } else {
            CreateVillageAction::run($villageData);

            Flux::toast(variant: 'success', text: 'Village created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-villageTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingVillageId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.village.index')
            ->layout('layouts.app', ['title' => __('Village')]);
    }

    protected function prefixedRules(?Village $village = null): array
    {
        $rules = StoreVillageRequest::villageRules($village);

        return collect($rules)
            ->mapWithKeys(fn (array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    protected function prefixedAttributes(): array
    {
        return collect(StoreVillageRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    public function resetForm(): void
    {
        $this->form = [
            'district_id' => null,
            'name' => null,
            'postal_code' => null,
        ];
    }
}

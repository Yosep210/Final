<?php

namespace App\Livewire\District;

use App\Actions\District\CreateDistrictAction;
use App\Actions\District\UpdateDistrictAction;
use App\Data\DistrictData;
use App\Http\Requests\District\StoreDistrictRequest;
use App\Models\City;
use App\Models\District;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('District')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingDistrictId = null;

    public array $cities = [];

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->cities = City::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingDistrictId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('district:edit')]
    public function edit(int $rowId): void
    {
        $district = District::query()->findOrFail($rowId);

        $this->editingDistrictId = $district->id;
        $this->form = [
            'name' => $district->name,
            'city_id' => $district->city_id,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $district = $this->editingDistrictId ? District::query()->findOrFail($this->editingDistrictId) : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($district)],
            [],
            $this->prefixedAttributes()
        );

        $districtData = DistrictData::fromArray($validated['form']);

        if ($district) {
            UpdateDistrictAction::run($districtData, $district);
            Flux::toast(variant: 'success', text: 'District updated successfully.');
        } else {
            CreateDistrictAction::run($districtData);
            Flux::toast(variant: 'success', text: 'District created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-districtTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingDistrictId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.district.index')
            ->layout('layouts.app', ['title' => __('District')]);
    }

    protected function prefixedRules(?District $district = null): array
    {
        $rules = StoreDistrictRequest::districtRules($district);

        return collect($rules)->mapWithKeys(fn (array $ruleSet, string $field) => ["form.$field" => $ruleSet])->all();
    }

    protected function prefixedAttributes(): array
    {
        return collect(StoreDistrictRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    private function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'city_id' => null,
        ];
    }
}

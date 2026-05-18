<?php

namespace App\Livewire\Province;

use App\Actions\Province\CreateProvinceAction;
use App\Actions\Province\UpdateProvinceAction;
use App\Data\ProvinceData;
use App\Http\Requests\Province\StoreProvinceRequest;
use App\Models\Country;
use App\Models\Province;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Province')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingProvinceId = null;

    public array $countries = [];

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->countries = Country::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingProvinceId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('province:edit')]
    public function edit(int $rowId): void
    {
        $province = Province::query()->findOrFail($rowId);

        $this->editingProvinceId = $province->id;
        $this->form = [
            'country_id' => $province->country_id,
            'name' => $province->name,
            'code' => $province->code,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $province = $this->editingProvinceId
            ? Province::query()->findOrFail($this->editingProvinceId)
            : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($province)],
            [],
            $this->prefixedAttributes(),
        );
        $provinceData = ProvinceData::fromArray($validated['form']);

        if ($province) {
            UpdateProvinceAction::run($province, $provinceData);

            Flux::toast(variant: 'success', text: 'Province updated successfully.');
        } else {
            CreateProvinceAction::run($provinceData);

            Flux::toast(variant: 'success', text: 'Province created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-provinceTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingProvinceId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.province.index')
            ->layout('layouts.app', ['title' => __('Province')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function prefixedRules(?Province $province = null): array
    {
        $rules = StoreProvinceRequest::provinceRules($province);

        return collect($rules)
            ->mapWithKeys(fn (array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function prefixedAttributes(): array
    {
        return collect(StoreProvinceRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    public function resetForm(): void
    {
        $this->form = [
            'country_id' => null,
            'name' => null,
            'code' => null,
        ];
    }
}

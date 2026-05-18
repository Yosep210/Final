<?php

namespace App\Livewire\City;

use App\Actions\City\CreateCityAction;
use App\Actions\City\UpdateCityAction;
use App\Data\CityData;
use App\Http\Requests\City\StoreCityRequest;
use App\Models\City;
use App\Models\Province;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('City')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingCityId = null;

    public array $provinces = [];

    public array $form = [];

    public function mount(): void
    {
        $this->provinces = Province::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $this->resetForm();
    }

    public function create(): void
    {
        $this->editingCityId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('city:edit')]
    public function edit(int $rowId): void
    {
        $city = City::query()->findOrFail($rowId);

        $this->editingCityId = $city->id;
        $this->form = [
            'name' => $city->name,
            'province_id' => $city->province_id,
            'type' => $city->type,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $city = $this->editingCityId ? City::query()->findOrFail($this->editingCityId) : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($city)],
            [],
            $this->prefixedAttributes()
        );

        $cityData = CityData::fromArray($validated['form']);

        if ($city) {
            UpdateCityAction::run($city, $cityData);
            Flux::toast(variant: 'success', text: 'City updated successfully.');
        } else {
            CreateCityAction::run($cityData);
            Flux::toast(variant: 'success', text: 'City created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-cityTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.city.index')
            ->layout('layouts.app', ['title' => 'City']);
    }

    protected function prefixedRules(?City $city = null): array
    {
        $rules = StoreCityRequest::cityRules($city);

        return collect($rules)
            ->mapWithKeys(fn(array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    protected function prefixedAttributes(): array
    {
        return collect(StoreCityRequest::attributeLabels())
            ->mapWithKeys(fn(string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => null,
            'province_id' => null,
            'type' => null,
        ];
    }
}

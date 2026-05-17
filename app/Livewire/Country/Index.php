<?php

namespace App\Livewire\Country;

use App\Actions\Country\CreateCountryAction;
use App\Actions\Country\UpdateCountryAction;
use App\Data\CountryData;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Models\Country;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Country')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingCountryId = null;

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
        $this->editingCountryId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('country:edit')]
    public function edit(int $countryId): void
    {
        $country = Country::query()->findOrFail($countryId);

        $this->editingCountryId = $country->id;
        $this->form = [
            'iso' => $country->iso,
            'name' => $country->name,
            'nice_name' => $country->nice_name,
            'iso3' => $country->iso3,
            'numcode' => $country->numcode,
            'phonecode' => $country->phonecode,
            'status' => $country->status,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $country = $this->editingCountryId
            ? Country::query()->findOrFail($this->editingCountryId)
            : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($country)],
            [],
            $this->prefixedAttributes(),
        );

        $countryData = CountryData::fromArray($validated['form']);

        if ($country) {
            UpdateCountryAction::run($country, $countryData);

            Flux::toast(variant: 'success', text: 'Country updated successfully.');
        } else {
            CreateCountryAction::run($countryData);

            Flux::toast(variant: 'success', text: 'Country created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-countryTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingCountryId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.country.index')
            ->layout('layouts.app', ['title' => __('Country')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function prefixedRules(?Country $country = null): array
    {
        $rules = StoreCountryRequest::countryRules($country);

        return collect($rules)
            ->mapWithKeys(fn (array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function prefixedAttributes(): array
    {
        return collect(StoreCountryRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'iso' => '',
            'name' => '',
            'nice_name' => '',
            'iso3' => '',
            'numcode' => '',
            'phonecode' => '',
            'status' => true,
        ];
    }
}

<?php

namespace App\Livewire\Bank;

use App\Actions\Bank\CreateBankAction;
use App\Actions\Bank\UpdateBankAction;
use App\Data\BankData;
use App\Http\Requests\Bank\StoreBankRequest;
use App\Models\Bank;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Bank')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $editingBankId = null;

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
        $this->editingBankId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showModal = true;
    }

    #[On('bank:edit')]
    public function edit(int $bankId): void
    {
        $bank = Bank::query()->findOrFail($bankId);

        $this->editingBankId = $bank->id;
        $this->form = [
            'name' => $bank->name,
            'code' => $bank->code,
            'type' => $bank->type,
            'flipcode' => $bank->flipcode,
            'espaycode' => $bank->espaycode,
            'linkitacode' => $bank->linkitacode,
            'logo' => $bank->logo,
        ];

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $bank = $this->editingBankId
            ? Bank::query()->findOrFail($this->editingBankId)
            : null;

        $validated = $this->validate(
            ['form' => ['array'], 'form.*' => ['nullable'], ...$this->prefixedRules($bank)],
            [],
            $this->prefixedAttributes(),
        );

        $bankData = BankData::fromArray($validated['form']);

        if ($bank) {
            UpdateBankAction::run($bank, $bankData);

            Flux::toast(variant: 'success', text: 'Bank updated successfully.');
        } else {
            CreateBankAction::run($bankData);

            Flux::toast(variant: 'success', text: 'Bank created successfully.');
        }

        $this->closeModal();
        $this->dispatch('pg:eventRefresh-bankTable');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingBankId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.bank.index')
            ->layout('layouts.app', ['title' => __('Bank')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function prefixedRules(?Bank $bank = null): array
    {
        $rules = StoreBankRequest::bankRules($bank);

        return collect($rules)
            ->mapWithKeys(fn (array $ruleSet, string $field) => ["form.$field" => $ruleSet])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function prefixedAttributes(): array
    {
        return collect(StoreBankRequest::attributeLabels())
            ->mapWithKeys(fn (string $label, string $field) => ["form.$field" => $label])
            ->all();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'code' => '',
            'type' => 'bank',
            'flipcode' => '',
            'espaycode' => '',
            'linkitacode' => '',
            'logo' => '',
        ];
    }
}

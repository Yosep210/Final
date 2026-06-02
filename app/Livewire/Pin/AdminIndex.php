<?php

namespace App\Livewire\Pin;

use App\Actions\Pin\GeneratePinsAction;
use App\Models\Member;
use App\Models\Pin;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage PINs')]
class AdminIndex extends Component
{
    use WithPagination;

    public string $searchSerial = '';

    public string $searchOwner = '';

    public string $filterStatus = 'all';

    // Bulk Generate Modal States
    public bool $showGenerateModal = false;

    public int $quantity = 10;

    public string $targetUsername = '';

    public ?string $targetName = null;

    public ?int $targetId = null;

    protected $queryString = [
        'searchSerial' => ['except' => ''],
        'searchOwner' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
    ];

    public function updating($property): void
    {
        if (in_array($property, ['searchSerial', 'searchOwner', 'filterStatus'])) {
            $this->resetPage();
        }
    }

    public function updatedTargetUsername(): void
    {
        if ($this->targetUsername === '') {
            $this->targetName = null;
            $this->targetId = null;

            return;
        }

        $member = Member::query()->where('username', $this->targetUsername)->first();
        if ($member) {
            $this->targetName = $member->name;
            $this->targetId = $member->id;
        } else {
            $this->targetName = 'Member not found';
            $this->targetId = null;
        }
    }

    public function openGenerateModal(): void
    {
        $this->quantity = 10;
        $this->targetUsername = '';
        $this->targetName = null;
        $this->targetId = null;
        $this->showGenerateModal = true;
    }

    public function generate(): void
    {
        $this->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'targetUsername' => ['nullable', 'string'],
        ]);

        if ($this->targetUsername !== '' && ! $this->targetId) {
            $this->addError('targetUsername', 'Please enter a valid active member username.');

            return;
        }

        GeneratePinsAction::run($this->quantity, $this->targetId);

        $this->showGenerateModal = false;
        Flux::toast(variant: 'success', text: "Successfully generated {$this->quantity} PINs.");
        $this->resetPage();
    }

    public function render()
    {
        $query = Pin::query()
            ->with(['owner', 'activatedMember'])
            ->orderBy('id', 'desc');

        if ($this->searchSerial !== '') {
            $query->where('serial_number', 'like', '%'.$this->searchSerial.'%');
        }

        if ($this->searchOwner !== '') {
            $query->whereHas('owner', function ($q) {
                $q->where('username', 'like', '%'.$this->searchOwner.'%')
                    ->orWhere('name', 'like', '%'.$this->searchOwner.'%');
            });
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.pin.admin-index', [
            'pins' => $query->paginate(15),
        ])->layout('layouts.app', ['title' => __('Manage PINs')]);
    }
}

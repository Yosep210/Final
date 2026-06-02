<?php

namespace App\Livewire\Pin;

use App\Actions\Pin\TransferPinsAction;
use App\Models\Member;
use App\Models\Pin;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My PINs')]
class MemberIndex extends Component
{
    use WithPagination;

    public string $searchSerial = '';

    public string $filterStatus = 'all';

    // Transfer Modal States
    public bool $showTransferModal = false;

    public string $recipientUsername = '';

    public ?string $recipientName = null;

    public ?int $recipientId = null;

    public array $selectedPinSerials = [];

    protected $queryString = [
        'searchSerial' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
    ];

    public function updating($property): void
    {
        if (in_array($property, ['searchSerial', 'filterStatus'])) {
            $this->resetPage();
        }
    }

    public function updatedRecipientUsername(): void
    {
        if ($this->recipientUsername === '') {
            $this->recipientName = null;
            $this->recipientId = null;

            return;
        }

        $member = Member::query()->where('username', $this->recipientUsername)->first();
        if ($member) {
            if ($member->id === auth()->id()) {
                $this->recipientName = 'You cannot transfer PINs to yourself';
                $this->recipientId = null;

                return;
            }

            if ($member->status !== 'active') {
                $this->recipientName = 'Recipient is inactive';
                $this->recipientId = null;

                return;
            }

            $this->recipientName = $member->name;
            $this->recipientId = $member->id;
        } else {
            $this->recipientName = 'Member not found';
            $this->recipientId = null;
        }
    }

    public function openTransferModal(): void
    {
        $this->recipientUsername = '';
        $this->recipientName = null;
        $this->recipientId = null;
        $this->selectedPinSerials = [];
        $this->showTransferModal = true;
    }

    public function transfer(): void
    {
        $this->validate([
            'recipientUsername' => ['required', 'string'],
            'selectedPinSerials' => ['required', 'array', 'min:1'],
        ], [
            'selectedPinSerials.required' => 'Please select at least one PIN to transfer.',
            'selectedPinSerials.min' => 'Please select at least one PIN to transfer.',
        ]);

        if (! $this->recipientId) {
            $this->addError('recipientUsername', 'Please enter a valid active member username.');

            return;
        }

        // Double check ownership and status of selected pins
        $ownedCount = Pin::query()
            ->where('owner_id', auth()->id())
            ->whereIn('serial_number', $this->selectedPinSerials)
            ->where('status', 'unused')
            ->count();

        if ($ownedCount !== count($this->selectedPinSerials)) {
            Flux::toast(variant: 'danger', text: 'Some of the selected PINs are invalid, already used, or not owned by you.');

            return;
        }

        TransferPinsAction::run(auth()->id(), $this->recipientId, $this->selectedPinSerials);

        $this->showTransferModal = false;
        Flux::toast(variant: 'success', text: 'Successfully transferred '.count($this->selectedPinSerials).' PINs to '.$this->recipientUsername);
        $this->resetPage();
    }

    public function render()
    {
        $query = Pin::query()
            ->with(['activatedMember'])
            ->where('owner_id', auth()->id())
            ->orderBy('id', 'desc');

        if ($this->searchSerial !== '') {
            $query->where('serial_number', 'like', '%'.$this->searchSerial.'%');
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Unused PINs list for transfer selection
        $availablePins = Pin::query()
            ->where('owner_id', auth()->id())
            ->where('status', 'unused')
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.pin.member-index', [
            'pins' => $query->paginate(15),
            'availablePins' => $availablePins,
        ])->layout('layouts.app', ['title' => __('My PINs')]);
    }
}

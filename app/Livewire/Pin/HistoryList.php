<?php

namespace App\Livewire\Pin;

use App\Models\Pin;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Riwayat PIN')]
final class HistoryList extends Component
{
    use WithPagination;

    public string $searchSerial = '';

    public string $searchUser = '';

    public string $filterStatus = 'all';

    protected $queryString = [
        'searchSerial' => ['except' => ''],
        'searchUser' => ['except' => ''],
        'filterStatus' => ['except' => 'all'],
    ];

    public function updating(): void
    {
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

        if ($this->searchUser !== '') {
            $query->where(function ($q) {
                $q->whereHas('owner', function ($sq) {
                    $sq->where('username', 'like', '%'.$this->searchUser.'%')
                        ->orWhere('name', 'like', '%'.$this->searchUser.'%');
                })->orWhereHas('activatedMember', function ($sq) {
                    $sq->where('username', 'like', '%'.$this->searchUser.'%')
                        ->orWhere('name', 'like', '%'.$this->searchUser.'%');
                });
            });
        }

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.pin.history-list', [
            'pins' => $query->paginate(15),
        ])->layout('layouts.app', ['title' => __('Riwayat PIN')]);
    }
}

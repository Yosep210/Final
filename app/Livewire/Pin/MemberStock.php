<?php

namespace App\Livewire\Pin;

use App\Models\Member;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Stock Produk Member')]
final class MemberStock extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Member::query()
            ->withCount([
                'pins as total_pins',
                'pins as active_pins' => fn ($q) => $q->where('status', 'unused'),
                'pins as used_pins' => fn ($q) => $q->where('status', 'used'),
            ])
            ->whereHas('pins');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('username', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.pin.member-stock', [
            'members' => $query->paginate(15),
        ])->layout('layouts.app', ['title' => __('Stock Produk Member')]);
    }
}

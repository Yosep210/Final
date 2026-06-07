<?php

namespace App\Livewire\Report;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Laporan Repeat Order (RO)')]
class Ro extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        if (!auth()->user() || !auth()->user()->hasRole('Admin')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.report.ro')
            ->layout('layouts.app', ['title' => __('Laporan Repeat Order (RO)')]);
    }
}

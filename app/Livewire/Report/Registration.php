<?php

namespace App\Livewire\Report;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Pendaftaran')]
class Registration extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Admin')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.report.registration')
            ->layout('layouts.app', ['title' => __('Laporan Pendaftaran')]);
    }
}

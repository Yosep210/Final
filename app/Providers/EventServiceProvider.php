<?php

namespace App\Providers;

use App\Events\MemberDemoted;
use App\Events\MemberPromoted;
use App\Events\MemberRegistered;
use App\Events\MemberVolumeUpdated;
use App\Listeners\CalculateCommissionOnVolumeUpdate;
use App\Listeners\CreateMemberNetworkListener;
use App\Listeners\UpdateMemberHierarchyListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MemberRegistered::class => [
            CreateMemberNetworkListener::class,
        ],
        MemberPromoted::class => [
            UpdateMemberHierarchyListener::class,
        ],
        MemberDemoted::class => [
            // Listener untuk handle demotion bisa ditambahkan di sini
        ],
        MemberVolumeUpdated::class => [
            CalculateCommissionOnVolumeUpdate::class,
        ],
    ];
}

<?php

namespace App\Livewire\Report;

use App\Models\MemberNetwork;
use App\Models\RewardConfig;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Laporan Reward')]
class Reward extends Component
{
    use AuthorizesRequests, WithPagination;

    public $username = '';

    public $name = '';

    public $activeTab = 'achievements'; // 'achievements' or 'configs'

    protected $queryString = [
        'username' => ['except' => ''],
        'name' => ['except' => ''],
        'activeTab' => ['except' => 'achievements'],
    ];

    public function mount(): void
    {
        if (! auth()->user() || ! auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        // Seed default rewards if database is empty
        if (RewardConfig::count() === 0) {
            RewardConfig::insert([
                [
                    'type' => 'lifetime',
                    'reward' => 'Handphone Android',
                    'nominal' => 1500000.00,
                    'point' => 100,
                    'packages' => '[]',
                    'rank' => 'Gold',
                    'message' => 'Reward Handphone Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'lifetime',
                    'reward' => 'Laptop Core i5',
                    'nominal' => 7000000.00,
                    'point' => 300,
                    'packages' => '[]',
                    'rank' => 'Sapphire',
                    'message' => 'Reward Laptop Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'lifetime',
                    'reward' => 'Sepeda Motor Honda Vario',
                    'nominal' => 22000000.00,
                    'point' => 1000,
                    'packages' => '[]',
                    'rank' => 'Ruby',
                    'message' => 'Reward Motor Vario Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'lifetime',
                    'reward' => 'Umroh Premium / Wisata Eropa',
                    'nominal' => 45000000.00,
                    'point' => 2500,
                    'packages' => '[]',
                    'rank' => 'Emerald',
                    'message' => 'Reward Umroh Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'lifetime',
                    'reward' => 'Mobil Mitsubishi Xpander',
                    'nominal' => 270000000.00,
                    'point' => 10000,
                    'packages' => '[]',
                    'rank' => 'Diamond',
                    'message' => 'Reward Mobil Xpander Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'type' => 'lifetime',
                    'reward' => 'Rumah Mewah Pondok Indah',
                    'nominal' => 1500000000.00,
                    'point' => 50000,
                    'packages' => '[]',
                    'rank' => 'Crown',
                    'message' => 'Reward Rumah Mewah Lifetime',
                    'is_lifetime' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function updating(): void
    {
        $this->resetPage();
    }

    public function setTab($tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->username = '';
        $this->name = '';
        $this->resetPage();
    }

    public function render()
    {
        $rewardConfigs = RewardConfig::where('is_active', true)->orderBy('point', 'asc')->get();

        if ($this->activeTab === 'configs') {
            // configs doesn't need pagination in this view
            return view('livewire.report.reward', [
                'rewardConfigs' => $rewardConfigs,
                'achievements' => null,
            ])->layout('layouts.app', ['title' => __('Laporan Reward')]);
        }

        // Achievements view query
        $query = MemberNetwork::query()
            ->join('members', 'member_networks.member_id', '=', 'members.id')
            ->select([
                'member_networks.member_id',
                'member_networks.left_volume',
                'member_networks.right_volume',
                'members.username',
                'members.name',
            ]);

        if ($this->username) {
            $query->where('members.username', 'like', '%'.$this->username.'%');
        }
        if ($this->name) {
            $query->where('members.name', 'like', '%'.$this->name.'%');
        }

        $results = $query->orderBy('member_networks.total_volume', 'desc')->paginate(10);

        // Process achievements for paginated list
        $achievements = $results->through(function ($net) use ($rewardConfigs) {
            // Assume 1 Point = 1,000 BV
            $leftPoints = (int) ($net->left_volume / 1000);
            $rightPoints = (int) ($net->right_volume / 1000);
            $points = min($leftPoints, $rightPoints);

            // Find qualifying rewards
            $achieved = [];
            $nextReward = null;

            foreach ($rewardConfigs as $cfg) {
                if ($points >= $cfg->point) {
                    $achieved[] = $cfg->reward;
                } else {
                    if (! $nextReward) {
                        $nextReward = (object) [
                            'reward' => $cfg->reward,
                            'req_points' => $cfg->point,
                            'cur_points' => $points,
                            'left_points' => $leftPoints,
                            'right_points' => $rightPoints,
                        ];
                    }
                }
            }

            $net->points = $points;
            $net->leftPoints = $leftPoints;
            $net->rightPoints = $rightPoints;
            $net->achieved_list = $achieved;
            $net->next_reward = $nextReward;

            return $net;
        });

        return view('livewire.report.reward', [
            'rewardConfigs' => $rewardConfigs,
            'achievements' => $achievements,
        ])->layout('layouts.app', ['title' => __('Laporan Reward')]);
    }
}

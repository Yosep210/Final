<?php

return [
    'Menu' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
        ],
        [
            'title' => 'My PINs',
            'icon' => 'key',
            'href' => 'my.pin.index',
        ],
        [
            'heading' => 'Data Member',
            'icon' => 'users',
            'route' => ['member.*', 'sponsor.*', 'group.*', 'network.*', 'generation.*'],
            'sub' => [
                [
                    'title' => 'Member List',
                    'href' => 'member.index',
                ],
                [
                    'title' => 'Sponsor List',
                    'href' => 'sponsor.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Group List',
                    'href' => 'group.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Gen List',
                    'href' => 'generation.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Jaringan Binary',
                    'href' => 'network.index',
                    'role' => 'Admin',
                ],
            ],
        ],
        [
            'heading' => 'Commision',
            'icon' => 'currency-dollar',
            'route' => ['commission.*', 'wallet.*', 'auto.ro.*', 'withdraw.*'],
            'sub' => [
                [
                    'title' => 'Bonues Detail',
                    'href' => 'commission.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Statement Commission',
                    'href' => 'commission.statement',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'eWallet',
                    'href' => 'wallet.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Auto RO',
                    'href' => 'auto.ro.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Withdraw',
                    'href' => 'withdraw.index',
                    'role' => 'Admin',
                ],
            ],
        ],
        [
            'heading' => 'Master Data',
            'icon' => 'lock-closed',
            'route' => ['role.*', 'permission.*', 'pin.*', 'country.*', 'province.*', 'city.*', 'district.*', 'village.*'],
            'sub' => [
                [
                    'title' => 'Role',
                    'href' => 'role.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Permission',
                    'href' => 'permission.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'PIN Management',
                    'href' => 'pin.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Country',
                    'href' => 'country.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Province',
                    'href' => 'province.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'City',
                    'href' => 'city.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'District',
                    'href' => 'district.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Village',
                    'href' => 'village.index',
                    'role' => 'Admin',
                ],
                [
                    'title' => 'Bank',
                    'href' => 'bank.index',
                    'role' => 'Admin',
                ],
            ],
        ],
    ],
];

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
            'route' => ['member.*', 'sponsor.*', 'group.*', 'network.*'],
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
                    'title' => 'Jaringan Binary',
                    'href' => 'network.index',
                    'role' => 'Admin',
                ],
            ],
        ],
        [
            'heading' => 'Admin',
            'icon' => 'lock-closed',
            'route' => ['role.*', 'permission.*', 'pin.*'],
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
            ],
        ],
        [
            'heading' => 'Data Address',
            'icon' => 'users',
            'route' => ['country.*', 'province.*', 'city.*', 'district.*', 'village.*'],
            'sub' => [
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
            ],
        ],
        [
            'heading' => 'Master Data',
            'icon' => 'circle-stack',
            'route' => ['area.*', 'bank.*', 'membership.*', 'package.*', 'rank.*', 'product.*', 'product-category.*', 'product-variant.*', 'supplier.*'],
            'sub' => [
                [
                    'title' => 'Transaction List',
                    'href' => 'dashboard',
                    // 'role' => 'Admin',
                ],
            ],
        ],
    ],
];

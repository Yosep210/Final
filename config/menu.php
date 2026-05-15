<?php

return [
    'Menu' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
        ],
        [
            'heading' => 'Data Address',
            'icon' => 'users',
            'route' => ['country.*'],
            'sub' => [
                [
                    'title' => 'Country',
                    'href' => 'country.index',
                    'role' => 'admin',
                ],
            ],
        ],
        // [
        //     'heading' => 'Data Member',
        //     'icon' => 'users',
        //     'route' => ['member.*'],
        //     'sub' => [
        //         [
        //             'title' => 'Create Member',
        //             'href' => 'member.create',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Member List',
        //             'href' => 'member.index',
        //         ],
        //     ],
        // ],
        [
            'heading' => 'Admin',
            'icon' => 'lock-closed',
            'route' => ['role.*', 'permission.*'],
            'sub' => [
                [
                    'title' => 'Role',
                    'href' => 'admin.role.index',
                    'role' => 'admin',
                ],
                [
                    'title' => 'Permission',
                    'href' => 'permission.index',
                    'role' => 'admin',
                ],
            ],
        ],
        // [
        //     'heading' => 'Master Data',
        //     'icon' => 'circle-stack',
        //     'route' => ['area.*', 'bank.*', 'city.*', 'country.*', 'regency.*', 'membership.*', 'package.*', 'province.*', 'rank.*', 'product.*', 'product-category.*', 'product-variant.*', 'supplier.*', 'village.*'],
        //     'sub' => [
        //         [
        //             'title' => 'Area',
        //             'href' => 'area.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Bank',
        //             'href' => 'bank.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'City',
        //             'href' => 'city.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Country',
        //             'href' => 'country.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Regency',
        //             'href' => 'regency.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Membership',
        //             'href' => 'membership.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Package',
        //             'href' => 'package.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Province',
        //             'href' => 'province.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Rank',
        //             'href' => 'rank.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Product',
        //             'href' => 'product.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Product Category',
        //             'href' => 'product-category.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Product Variant',
        //             'href' => 'product-variant.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Supplier',
        //             'href' => 'supplier.index',
        //             'role' => 'admin',
        //         ],
        //         [
        //             'title' => 'Village',
        //             'href' => 'village.index',
        //             'role' => 'admin',
        //         ],
        //     ],
        // ],
        // [
        //     'heading' => 'Transactions',
        //     'icon' => 'currency-dollar',
        //     'route' => ['transaction.*'],
        //     'sub' => [
        //         [
        //             'title' => 'Transaction List',
        //             'href' => 'dashboard',
        //             'role' => 'admin',
        //         ],
        //     ],
        // ],
    ],
];

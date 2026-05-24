<?php

return [
    'Menu' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
        ],
        [
            'heading' => 'Data Member',
            'icon' => 'users',
            'route' => ['member.*'],
            'sub' => [
                [
                    'title' => 'Member List',
                    'href' => 'member.index',
                ],
            ],
        ],
        [
            'heading' => 'Admin',
            'icon' => 'lock-closed',
            'route' => ['role.*', 'permission.*'],
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
            // 'sub' => [
            //         [
            //             'title' => 'Area',
            //             'href' => 'area.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Bank',
            //             'href' => 'bank.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Membership',
            //             'href' => 'membership.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Package',
            //             'href' => 'package.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Rank',
            //             'href' => 'rank.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Product',
            //             'href' => 'product.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Product Category',
            //             'href' => 'product-category.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Product Variant',
            //             'href' => 'product-variant.index',
            // 'role' => 'Admin',
            //         ],
            //         [
            //             'title' => 'Supplier',
            //             'href' => 'supplier.index',
            // 'role' => 'Admin',
            //         ],
            //     ],
        ],
    ],
];

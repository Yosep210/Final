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
                    // 'role' => 'admin',
                ],
                [
                    'title' => 'Permission',
                    'href' => 'permission.index',
                    //     // 'role' => 'admin',
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
                    // 'role' => 'admin',
                ],
                [
                    'title' => 'Province',
                    'href' => 'province.index',
                    //             'role' => 'admin',
                ],
                [
                    'title' => 'City',
                    'href' => 'city.index',
                    //             'role' => 'admin',
                ],
                [
                    'title' => 'District',
                    'href' => 'district.index',
                    //             'role' => 'admin',
                ],
                [
                    'title' => 'Village',
                    'href' => 'village.index',
                    //             'role' => 'admin',
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
                    // 'role' => 'admin',
                ],
            ],
            // 'sub' => [
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
            //     ],
        ],
        [
            'heading' => 'Transactions',
            'icon' => 'currency-dollar',
            'route' => ['transaction.*'],
            'sub' => [
                [
                    'title' => 'Transaction List',
                    'href' => 'dashboard',
                    // 'role' => 'admin',
                ],
            ],
        ],
    ],
];

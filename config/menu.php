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
            'title' => 'My Wallet',
            'icon' => 'currency-dollar',
            'href' => 'my.wallet.index',
        ],
        [
            'heading' => 'Data Member',
            'icon' => 'users',
            'route' => ['member.*', 'sponsor.*', 'group.*', 'network.*', 'generation.*'],
            'sub' => [
                [
                    'title' => 'Member List',
                    'href' => 'member.index',
                    'permission' => 'access-member-list',
                ],
                [
                    'title' => 'Sponsor List',
                    'href' => 'sponsor.index',
                    'permission' => 'access-member-sponsor',
                ],
                [
                    'title' => 'Group List',
                    'href' => 'group.index',
                    'permission' => 'access-member-list',
                ],
                [
                    'title' => 'Gen List',
                    'href' => 'generation.index',
                    'permission' => 'access-member-generation',
                ],
                [
                    'title' => 'Jaringan Binary',
                    'href' => 'network.index',
                    'permission' => 'access-member-tree',
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
                    'permission' => 'access-finance-bonus',
                ],
                [
                    'title' => 'Statement Commission',
                    'href' => 'commission.statement',
                    'permission' => 'access-finance-statement',
                ],
                [
                    'title' => 'eWallet',
                    'href' => 'wallet.index',
                    'permission' => 'access-finance-ewallet',
                ],
                [
                    'title' => 'Auto RO',
                    'href' => 'auto.ro.index',
                    'permission' => 'access-finance-autoro',
                ],
                [
                    'title' => 'Withdraw',
                    'href' => 'withdraw.index',
                    'permission' => 'access-finance-withdraw',
                ],
            ],
        ],
        [
            'heading' => 'Data Produk Order',
            'icon' => 'shopping-bag',
            'route' => ['pin.*', 'product.order.*'],
            'sub' => [
                [
                    'title' => 'Kirim Produk',
                    'href' => 'pin.index',
                    'permission' => 'access-pin-generate',
                ],
                [
                    'title' => 'Stock Produk Member',
                    'href' => 'pin.stock.index',
                    'permission' => 'access-pin-stock',
                ],
                [
                    'title' => 'Riwayat PIN',
                    'href' => 'pin.history.index',
                    'permission' => 'access-pin-transfer',
                ],
                [
                    'title' => 'Orderan ke Perusahaan',
                    'href' => 'product.order.index',
                    'permission' => 'access-pin-order',
                ],
            ],
        ],
        [
            'heading' => 'Laporan',
            'icon' => 'document-text',
            'route' => ['report.*'],
            'sub' => [
                [
                    'title' => 'Pendaftaran',
                    'href' => 'report.registration',
                    'permission' => 'access-report-registration',
                ],
                [
                    'title' => 'Repeat Order (RO)',
                    'href' => 'report.ro',
                    'permission' => 'access-report-ro',
                ],
                [
                    'title' => 'Pairing Qualified',
                    'href' => 'report.pairing',
                    'permission' => 'access-report-pairing',
                ],
                [
                    'title' => 'Omzet Posting Harian',
                    'href' => 'report.omzet-daily',
                    'permission' => 'access-report-omzet-posting-daily',
                ],
                [
                    'title' => 'Omzet Posting Bulanan',
                    'href' => 'report.omzet-monthly',
                    'permission' => 'access-report-omzet-posting-monthly',
                ],
                [
                    'title' => 'Omzet Order',
                    'href' => 'report.omzet-order',
                    'permission' => 'access-report-omzet-order',
                ],
                [
                    'title' => 'Budgeting',
                    'href' => 'report.budgeting',
                    'permission' => 'access-report-budgeting',
                ],
                [
                    'title' => 'Pajak',
                    'href' => 'report.tax',
                    'permission' => 'access-report-tax',
                ],
                [
                    'title' => 'Reward',
                    'href' => 'report.reward',
                    'permission' => 'access-report-reward',
                ],
            ],
        ],
        [
            'heading' => 'Master Data',
            'icon' => 'lock-closed',
            'route' => ['bank.*', 'country.*', 'province.*', 'city.*', 'district.*', 'village.*', 'role.*', 'permission.*'],
            'sub' => [
                [
                    'title' => 'Bank',
                    'href' => 'bank.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'Country',
                    'href' => 'country.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'Province',
                    'href' => 'province.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'City',
                    'href' => 'city.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'District',
                    'href' => 'district.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'Village',
                    'href' => 'village.index',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'Role',
                    'href' => 'role.index',
                    'permission' => 'access-setting-staff',
                ],
                [
                    'title' => 'Permission',
                    'href' => 'permission.index',
                    'permission' => 'access-setting-staff',
                ],
            ],
        ],
    ],
];

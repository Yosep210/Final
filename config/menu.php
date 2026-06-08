<?php

return [
    'admin' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
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
            'heading' => 'Master Product',
            'icon' => 'shopping-cart',
            'route' => ['master.product.*', 'master.supplier.*', 'master.purchase.*', 'master.stock.*'],
            'sub' => [
                [
                    'title' => 'List Product',
                    'href' => 'master.product.list',
                    'permission' => 'access-master-product',
                ],
                [
                    'title' => 'Supplier',
                    'href' => 'master.supplier.list',
                    'permission' => 'access-master-supplier',
                ],
                [
                    'title' => 'Pembelian',
                    'href' => 'master.purchase.list',
                    'permission' => 'access-master-purchase',
                ],
                [
                    'title' => 'Laporan Stok',
                    'href' => 'master.stock.list',
                    'permission' => 'access-master-stock',
                ],
                [
                    'title' => 'Penyesuaian Stok',
                    'href' => 'master.stock.opname',
                    'permission' => 'access-master-stock-opname',
                ],
            ],
        ],
        [
            'heading' => 'Master Staff',
            'icon' => 'users',
            'route' => ['staff.manage'],
            'sub' => [
                [
                    'title' => 'Manage Staff',
                    'href' => 'staff.manage',
                    'permission' => 'access-setting-staff',
                ],
            ],
        ],
        [
            'heading' => 'News',
            'icon' => 'chat-bubble-left-right',
            'route' => ['news.index'],
            'sub' => [
                [
                    'title' => 'News List',
                    'href' => 'news.index',
                    'permission' => 'access-news',
                ],
            ],
        ],
        [
            'heading' => 'Master Setting',
            'icon' => 'cog',
            'route' => ['setting.*'],
            'sub' => [
                [
                    'title' => 'General Setting',
                    'href' => 'setting.general',
                    'permission' => 'access-setting-general',
                ],
                [
                    'title' => 'Notification Setting',
                    'href' => 'setting.notification',
                    'permission' => 'access-setting-notification',
                ],
                [
                    'title' => 'Reward Setting',
                    'href' => 'setting.reward',
                    'permission' => 'access-setting-reward',
                ],
                [
                    'title' => 'Withdraw Setting',
                    'href' => 'setting.withdraw',
                    'permission' => 'access-setting-withdraw',
                ],
                [
                    'title' => 'Video',
                    'href' => 'setting.video',
                    'permission' => 'access-setting-general',
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
    'member' => [
        [
            'title' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
        ],
        [
            'heading' => 'Member',
            'icon' => 'user-plus',
            'route' => ['member.ro', 'member.registry'],
            'sub' => [
                [
                    'title' => 'Repeat Order (RO)',
                    'href' => 'member.ro',
                ],
                [
                    'title' => 'List Registrasi',
                    'href' => 'member.registry',
                ],
            ],
        ],
        [
            'heading' => 'Jaringan',
            'icon' => 'users',
            'route' => ['network.index', 'generation.index'],
            'sub' => [
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
            'heading' => 'Financial',
            'icon' => 'currency-dollar',
            'route' => ['my.wallet.index', 'commission.bonus', 'commission.statement', 'commission.ewallet', 'commission.autoro', 'commission.autorosaldo', 'commission.withdraw'],
            'sub' => [
                [
                    'title' => 'My Wallet',
                    'href' => 'my.wallet.index',
                    'permission' => 'access-finance-ewallet',
                ],
                [
                    'title' => 'Detail Bonus',
                    'href' => 'commission.bonus',
                ],
                [
                    'title' => 'Statement Commission',
                    'href' => 'commission.statement',
                ],
                [
                    'title' => 'eWallet',
                    'href' => 'commission.ewallet',
                ],
                [
                    'title' => 'Auto RO',
                    'href' => 'commission.autoro',
                ],
                [
                    'title' => 'Saldo Auto RO',
                    'href' => 'commission.autorosaldo',
                ],
                [
                    'title' => 'Withdraw',
                    'href' => 'commission.withdraw',
                ],
            ],
        ],
        [
            'heading' => 'PIN',
            'icon' => 'key',
            'route' => ['my.pin.index', 'pin.datalists', 'pin.transfer', 'pin.history'],
            'sub' => [
                [
                    'title' => 'My PINs',
                    'href' => 'my.pin.index',
                    'permission' => 'access-pin-stock',
                ],
                [
                    'title' => 'List PIN',
                    'href' => 'pin.datalists',
                ],
                [
                    'title' => 'Transfer PIN',
                    'href' => 'pin.transfer',
                ],
                [
                    'title' => 'Riwayat PIN',
                    'href' => 'pin.history',
                ],
            ],
        ],
        [
            'heading' => 'Belanja',
            'icon' => 'shopping-cart',
            'route' => ['shop.index', 'shop.orders', 'shop.checkout'],
            'sub' => [
                [
                    'title' => 'Toko Belanja',
                    'href' => 'shop.index',
                ],
                [
                    'title' => 'Riwayat Belanja Saya',
                    'href' => 'shop.orders',
                ],
            ],
        ],
        [
            'heading' => 'Laporan',
            'icon' => 'document-text',
            'route' => ['report.registration', 'report.ro', 'report.pairing', 'report.reward'],
            'sub' => [
                [
                    'title' => 'Pendaftaran',
                    'href' => 'report.registration',
                ],
                [
                    'title' => 'Repeat Order (RO)',
                    'href' => 'report.ro',
                ],
                [
                    'title' => 'Pairing',
                    'href' => 'report.pairing',
                ],
                [
                    'title' => 'Reward',
                    'href' => 'report.reward',
                ],
            ],
        ],
    ],
];

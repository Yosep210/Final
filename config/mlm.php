<?php

/**
 * MLM (Multi-Level Marketing) System Configuration
 *
 * This file configures all MLM-related settings including:
 * - Binary pairing rates and dynamic caps
 * - Dynamic sponsor rates based on recruits
 * - Volume calculation and registration BV rules
 * - Tax rates, withdrawal limits/fees, and Auto-RO caps
 */

return [
    /**
     * Default Role for New Members
     */
    'default_member_role' => 'Member',

    /**
     * Commission Settings
     */
    'commission' => [
        /**
         * Kurs BV to IDR (1 BV = Rp 1.000)
         */
        'kurs_bv' => 1000,

        /**
         * Standard Registration Package Volume (BV)
         */
        'registration_bv' => 2500,

        /**
         * Pairing Rate (6% of matched volume)
         */
        'pairing_rate' => 6,

        /**
         * Dynamic Daily Pairing Caps (IDR) based on number of active sponsors
         */
        'pairing_caps' => [
            1 => 5000000,  // >= 1 sponsor: Rp 5.000.000 / day
            2 => 10000000, // >= 2 sponsors: Rp 10.000.000 / day
            3 => 20000000, // >= 3 sponsors: Rp 20.000.000 / day
        ],

        /**
         * Dynamic Sponsor Percentages based on number of active sponsors
         */
        'sponsor_rates' => [
            1 => 14, // >= 1 sponsor: 14% of package BV
            2 => 18, // >= 2 sponsors: 18% of package BV
            3 => 24, // >= 3 sponsors: 24% of package BV
        ],

        /**
         * Tax Rate on gross commission (2.5%)
         */
        'tax_rate' => 2.5,

        /**
         * Minimum matched volume to qualify for pairing commission
         */
        'minimum_volume' => 100,
    ],

    /**
     * Rank Configuration
     * Requirements for each rank based on personal recruits and volume
     */
    'ranks' => [
        'member' => [
            'personal_recruits' => 0,
            'left_volume' => 0,
            'right_volume' => 0,
        ],
        'star' => [
            'personal_recruits' => 3, // star achieved by recruiting 3 members
            'left_volume' => 0,
            'right_volume' => 0,
        ],
    ],

    /**
     * Generation Bonus Configuration (Sponsor referral chain)
     */
    'generation_bonus' => [
        'enabled' => true,
        'rate' => 0.6,        // 0.6% per level
        'max_levels' => 12,   // Up to 12 levels
    ],

    /**
     * Unilevel Configuration (Placement parent chain)
     */
    'unilevel' => [
        'enabled' => true,
        'rate' => 0.4,        // 0.4% per level
        'max_levels' => 20,   // Up to 20 levels
    ],

    /**
     * Fast Start Bonus (Disabled by default)
     */
    'faststart_bonus' => [
        'enabled' => false,
        'amount' => 0,
        'days_active' => 30,
    ],

    /**
     * Network Settings
     */
    'network' => [
        'binary_enabled' => true,
        'auto_placement' => true,
        'placement_algorithm' => 'breadth_first',
    ],

    /**
     * Registration Settings
     */
    'registration_requires_pin' => true,

    /**
     * Payout & Withdrawal Settings
     */
    'payout' => [
        'minimum_commission' => 50000,  // Minimum withdraw Rp 50.000
        'fee' => 5000,                  // Flat withdrawal fee Rp 5.000
        'payment_methods' => [
            'bank_transfer' => 'Bank Transfer',
        ],
        'processing_days' => 5,
    ],

    /**
     * Demotion Settings
     */
    'demotion' => [
        'enabled' => true,
        'check_frequency' => 'monthly',
        'grace_period_months' => 0,
    ],

    /**
     * Volume Reset Settings
     */
    'volume' => [
        'reset_frequency' => 'monthly',
        'include_member_own_volume' => false,
        'count_downline_depth' => null,
    ],

    /**
     * Audit & Compliance
     */
    'audit' => [
        'log_all_transactions' => true,
        'log_commissions' => true,
        'retention_days' => 2555,
    ],

    /**
     * Auto Repeat Order (Auto-RO) Settings
     */
    'auto_ro' => [
        'percent' => 20,                         // 20% split from pairing bonus
        'monthly_max' => 3300000.00,             // Max Rp 3.300.000 Auto-RO split per calendar month
        'package_price_threshold' => 1000000.00, // Threshold to trigger auto-purchase (Rp 1.000.000)
        'default_package_sku' => 'RO-PACKAGE',
    ],

    /**
     * Stockist Settings
     */
    'stockist' => [
        'minimum_order' => [
            1 => 7500000.00,  // Mobile Stockist
            2 => 7500000.00,  // Stockist
            3 => 90000000.00, // Master Stockist
        ],
        'minimum_order_by_member_id' => [
            3340 => 7500000.00,
            2667 => 54500000.00,
            2052 => 54000000.00,
        ],
        'discount' => [
            1 => 0.0,
            2 => 0.0,
            3 => 0.0,
        ],
        'minimum_order_discount' => [
            5000000 => 0.0,
            25000000 => 0.0,
            150000000 => 0.0,
        ],
    ],

    /**
     * Shipping & Rajaongkir Settings
     */
    'shipping' => [
        'rajaongkir_active' => true,
        'rajaongkir_origin' => 151, // Default Origin City ID
        'rajaongkir_origin_type' => 'city',
        'rajaongkir_token' => '14086d4d07f3a24feff8a2fad320d909',
        'rajaongkir_url' => 'https://rajaongkir.komerce.id/api/v1/',
    ],
];

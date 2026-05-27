<?php

/**
 * MLM (Multi-Level Marketing) System Configuration
 *
 * This file configures all MLM-related settings including:
 * - Binary commission rates by rank
 * - Rank requirements and thresholds
 * - Volume calculation rules
 * - Tax rates and financial settings
 */

return [
    /**
     * Default Role for New Members
     */
    'default_member_role' => 'Member',

    /**
     * Commission Rates Configuration
     * Percentage rates applied based on member rank
     */
    'commission' => [
        'rates' => [
            'member' => 3,        // 3% commission
            'bronze' => 5,        // 5% commission
            'silver' => 7,        // 7% commission
            'gold' => 10,         // 10% commission
        ],
        'tax_rate' => 15,        // 15% tax on gross commission
        'minimum_volume' => 100, // Minimum matched volume to qualify for commission
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
        'bronze' => [
            'personal_recruits' => 1,
            'left_volume' => 0,
            'right_volume' => 0,
        ],
        'silver' => [
            'personal_recruits' => 2,
            'left_volume' => 500,
            'right_volume' => 500,
        ],
        'gold' => [
            'personal_recruits' => 4,
            'left_volume' => 2000,
            'right_volume' => 2000,
        ],
    ],

    /**
     * Generation Bonus Configuration
     * Additional bonuses for deep network levels
     */
    'generation_bonus' => [
        'enabled' => false, // Set to true to enable generation bonuses
        'max_levels' => 10,
        'rates' => [
            1 => 0,  // 0% bonus on level 1 (already counted in binary)
            2 => 1,  // 1% bonus on level 2
            3 => 0.5,
            4 => 0.3,
            5 => 0.1,
        ],
    ],

    /**
     * Unilevel Configuration
     * Flat bonus based on direct recruits' volume
     */
    'unilevel' => [
        'enabled' => false, // Set to true to enable unilevel bonuses
        'rate' => 2,        // 2% on direct downline volume
    ],

    /**
     * Fast Start Bonus
     * Bonus given when recruiting new members within specific timeframe
     */
    'faststart_bonus' => [
        'enabled' => false,
        'amount' => 0,           // Flat amount per new recruit
        'days_active' => 30,     // Days to qualify as "fast start"
    ],

    /**
     * Network Settings
     */
    'network' => [
        'binary_enabled' => true,
        'auto_placement' => true,    // Auto-place new recruits in binary tree
        'placement_algorithm' => 'breadth_first', // 'breadth_first' or 'depth_first'
    ],

    /**
     * Payout Settings
     */
    'payout' => [
        'minimum_commission' => 100,  // Minimum commission to request payout
        'payment_methods' => [
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'direct_deposit' => 'Direct Deposit',
            'cryptocurrency' => 'Cryptocurrency',
        ],
        'processing_days' => 5,       // Days to process payout
    ],

    /**
     * Demotion Settings
     * What happens when member doesn't meet rank requirements
     */
    'demotion' => [
        'enabled' => true,
        'check_frequency' => 'monthly', // 'monthly', 'quarterly', 'annually'
        'grace_period_months' => 0,     // Allow member to fail for N months before demotion
    ],

    /**
     * Volume Reset Settings
     * When and how volumes are calculated/reset
     */
    'volume' => [
        'reset_frequency' => 'monthly',      // 'daily', 'weekly', 'monthly', 'annually'
        'include_member_own_volume' => false, // Include member's personal purchases in volume
        'count_downline_depth' => null,      // null = unlimited depth, or specify max levels
    ],

    /**
     * Audit & Compliance
     */
    'audit' => [
        'log_all_transactions' => true,
        'log_commissions' => true,
        'retention_days' => 2555, // 7 years for compliance
    ],
];

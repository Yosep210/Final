<?php

namespace App\Services;

use App\Models\Member;

class ProductOrderService
{
    /**
     * Validate if the member meets the minimum order requirement.
     * Only applies if the member is a stockist (type > 0).
     */
    public function validateCheckoutMinimumOrder(Member $member, float $subtotal): array
    {
        if ($member->type <= 0) {
            return [
                'valid' => true,
                'min_required' => 0.0,
                'message' => 'Member has no minimum order requirement.',
            ];
        }

        // Check for specific override by member ID
        $memberOverrides = config('mlm.stockist.minimum_order_by_member_id', []);
        $minRequired = 0.0;

        if (isset($memberOverrides[$member->id])) {
            $minRequired = (float) $memberOverrides[$member->id];
        } else {
            $typeMinOrders = config('mlm.stockist.minimum_order', []);
            $minRequired = (float) ($typeMinOrders[$member->type] ?? 0.0);
        }

        if ($subtotal < $minRequired) {
            return [
                'valid' => false,
                'min_required' => $minRequired,
                'message' => sprintf(
                    'Minimal belanja untuk tipe stockist Anda adalah Rp %s.',
                    number_format($minRequired, 0, ',', '.')
                ),
            ];
        }

        return [
            'valid' => true,
            'min_required' => $minRequired,
            'message' => 'Minimum order requirement met.',
        ];
    }

    /**
     * Calculate stockist discount based on member type and order subtotal.
     * Matches the legacy CI3 logic structure.
     */
    public function calculateStockistDiscount(Member $member, float $subtotal): float
    {
        if ($member->type <= 0) {
            return 0.0;
        }

        // Get base stockist type discount limit
        $typeDiscounts = config('mlm.stockist.discount', []);
        $maxDiscountPercent = (float) ($typeDiscounts[$member->type] ?? 0.0);
        $maxDiscountPercent = max(0.0, min(100.0, $maxDiscountPercent));

        $discountPercent = $maxDiscountPercent;

        // Apply tiered minimum order discount if config exists
        $tieredDiscounts = config('mlm.stockist.minimum_order_discount', []);
        if (! empty($tieredDiscounts) && is_array($tieredDiscounts)) {
            $qualifyingPercent = 0.0;
            // Tiered config is sorted by threshold asc, find the highest threshold the order qualifies for
            ksort($tieredDiscounts);
            foreach ($tieredDiscounts as $threshold => $percent) {
                if ($subtotal >= (float) $threshold) {
                    $qualifyingPercent = (float) $percent;
                }
            }

            // The discount is capped at the member's base type discount
            $discountPercent = min($qualifyingPercent, $maxDiscountPercent);
            $discountPercent = max(0.0, $discountPercent);
        }

        if ($discountPercent > 0.0) {
            return ($subtotal * $discountPercent) / 100.0;
        }

        return 0.0;
    }
}

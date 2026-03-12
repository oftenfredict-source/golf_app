<?php

namespace App\Helpers;

class PaymentMethodHelper
{
    /**
     * Standard payment methods mapping
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'CASH',
            'mobile_money' => 'MOBILE MONEY (LIPA NAMBA)',
            'bank' => 'BANK',
            'balance' => 'MEMBER BALANCE',
            'card' => 'CARD (POS)',
        ];
    }

    /**
     * Get display name for payment method
     */
    public static function getDisplayName($method)
    {
        $methods = self::getPaymentMethods();
        return $methods[$method] ?? strtoupper(str_replace('_', ' ', $method));
    }

    /**
     * Get badge class for payment method
     */
    public static function getBadgeClass($method)
    {
        $classes = [
            'cash' => 'bg-label-success',
            'mobile_money' => 'bg-label-warning',
            'bank' => 'bg-label-info',
            'balance' => 'bg-label-primary',
            'card' => 'bg-label-secondary',
        ];
        return $classes[$method] ?? 'bg-label-secondary';
    }

    /**
     * Standardize payment method value
     */
    public static function standardize($method)
    {
        $mapping = [
            'mobile' => 'mobile_money',
            'mobile money' => 'mobile_money',
            'lipa namba' => 'mobile_money',
            'bank_transfer' => 'bank',
            'upi' => 'balance', // For backward compatibility
        ];
        
        $normalized = strtolower(str_replace([' ', '-'], '_', $method));
        return $mapping[$normalized] ?? $normalized;
    }
}



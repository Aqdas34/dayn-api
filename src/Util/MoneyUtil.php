<?php

declare(strict_types=1);

namespace App\Util;

/**
 * MoneyUtil handles financial calculations using Kobo (integers)
 * to avoid floating-point precision issues.
 */
class MoneyUtil
{
    /**
     * Convert Naira amount to Kobo (int)
     */
    public static function toKobo(float|string|int $amount): int
    {
        return (int) round((float)$amount * 100);
    }

    /**
     * Convert Kobo (int) to Naira (float string)
     */
    public static function toNaira(int|string $kobo): string
    {
        return number_format((int)$kobo / 100, 2, '.', '');
    }

    /**
     * Safely add two Kobo amounts
     */
    public static function add(int|string $a, int|string $b): int
    {
        return (int)$a + (int)$b;
    }

    /**
     * Safely subtract two Kobo amounts
     */
    public static function subtract(int|string $a, int|string $b): int
    {
        return (int)$a - (int)$b;
    }

    /**
     * Validate if an amount is positive
     */
    public static function isPositive(int|string $amount): bool
    {
        return (int)$amount > 0;
    }
}

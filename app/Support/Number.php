<?php

namespace App\Support;

class Number
{
    /**
     * Format an emission figure without trailing zeros, keeping enough
     * precision for the small factors used in LCI data.
     */
    public static function emission(float|int|string|null $value, int $precision = 4): string
    {
        $number = (float) $value;

        if ($number === 0.0) {
            return '0';
        }

        $formatted = rtrim(rtrim(number_format($number, $precision, '.', ','), '0'), '.');

        return $formatted === '' ? '0' : $formatted;
    }

    /**
     * Format a quantity for tables: thousands separators, up to $precision
     * decimals, and no trailing zeros.
     */
    public static function quantity(float|int|string|null $value, int $precision = 4): string
    {
        $number = (float) $value;

        if (! is_finite($number)) {
            return '0';
        }

        if ($number === 0.0) {
            return '0';
        }

        $formatted = rtrim(rtrim(number_format($number, $precision, ',', '.'), '0'), ',');

        return $formatted === '' ? '0' : $formatted;
    }

    /**
     * Format an emission figure in Indonesian notation without trailing
     * zeros (4 → "4", 3,8 stays "3,8").
     */
    public static function emissionId(float|int|string|null $value, int $precision = 4): string
    {
        $number = (float) $value;

        if (! is_finite($number)) {
            return '0';
        }

        if ($number === 0.0) {
            return '0';
        }

        $formatted = rtrim(rtrim(number_format($number, $precision, ',', '.'), '0'), ',');

        return $formatted === '' ? '0' : $formatted;
    }

    /**
     * Format a share percentage without trailing zeros (12,5 stays "12,5").
     */
    public static function percent(float|int|string|null $value, int $precision = 1): string
    {
        return self::emissionId($value, $precision);
    }
}

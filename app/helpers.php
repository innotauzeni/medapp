<?php

use App\Services\SettingsService;

if (!function_exists('site_setting')) {
    /** Get a site setting value (from cached settings). */
    function site_setting(string $key, mixed $default = null): mixed
    {
        try {
            return app(SettingsService::class)->get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('format_money')) {
    /**
     * Format a numeric amount using the site's configured currency symbol.
     * Examples: format_money(1450)        => "R 1,450.00"
     *           format_money(1450, 0)     => "R 1,450"
     */
    function format_money(int|float|string|null $amount, int $decimals = 2): string
    {
        $amount = (float) ($amount ?? 0);
        $symbol = site_setting('currency_symbol', 'R');
        $position = site_setting('currency_symbol_position', 'before'); // before|after
        $formatted = number_format($amount, $decimals, '.', ',');

        return $position === 'after'
            ? "{$formatted} {$symbol}"
            : "{$symbol} {$formatted}";
    }
}

if (!function_exists('currency_code')) {
    function currency_code(): string
    {
        return (string) site_setting('currency_code', 'ZAR');
    }
}

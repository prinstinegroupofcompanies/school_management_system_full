<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use NumberFormatter;

class CurrencyService
{
    /**
     * Default currency.
     */
    protected string $defaultCurrency = 'LRD';

    /**
     * Supported currencies.
     */
    protected array $supportedCurrencies = [
        'LRD' => [
            'name' => 'Liberian Dollar',
            'symbol' => 'L$',
            'decimal_places' => 2,
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'decimal_places' => 2,
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'decimal_places' => 2,
        ],
    ];

    /**
     * Format currency amount.
     */
    public function format(float $amount, string $currency = null, string $locale = null): string
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        $locale = $locale ?: app()->getLocale();

        if (!$this->isSupported($currency)) {
            $currency = $this->getDefaultCurrency();
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $this->getSymbol($currency));
        
        return $formatter->formatCurrency($amount, $currency);
    }

    /**
     * Get currency symbol.
     */
    public function getSymbol(string $currency = null): string
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        
        return $this->supportedCurrencies[$currency]['symbol'] ?? 'L$';
    }

    /**
     * Get currency code.
     */
    public function getCode(string $currency = null): string
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        
        return $currency;
    }

    /**
     * Get currency name.
     */
    public function getName(string $currency = null): string
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        
        return $this->supportedCurrencies[$currency]['name'] ?? 'Liberian Dollar';
    }

    /**
     * Get supported currencies.
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    /**
     * Check if currency is supported.
     */
    public function isSupported(string $currency): bool
    {
        return isset($this->supportedCurrencies[$currency]);
    }

    /**
     * Convert currency amount.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = $this->getExchangeRate($from, $to);
        
        return $amount * $rate;
    }

    /**
     * Get exchange rate.
     */
    public function getExchangeRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        // Cache exchange rates for 1 hour
        $cacheKey = "exchange_rate_{$from}_{$to}";
        
        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            // Default exchange rates (in real app, these would come from an API)
            $rates = [
                'LRD_USD' => 0.0055, // 1 LRD = 0.0055 USD
                'LRD_EUR' => 0.0051, // 1 LRD = 0.0051 EUR
                'LRD_GBP' => 0.0044, // 1 LRD = 0.0044 GBP
                'USD_LRD' => 181.82,  // 1 USD = 181.82 LRD
                'EUR_LRD' => 196.08,  // 1 EUR = 196.08 LRD
                'GBP_LRD' => 227.27,  // 1 GBP = 227.27 LRD
            ];

            $key = "{$from}_{$to}";
            
            if (isset($rates[$key])) {
                return $rates[$key];
            }

            // If direct rate not available, try reverse rate
            $reverseKey = "{$to}_{$from}";
            if (isset($rates[$reverseKey])) {
                return 1 / $rates[$reverseKey];
            }

            // Default fallback
            return 1.0;
        });
    }

    /**
     * Get default currency.
     */
    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    /**
     * Set default currency.
     */
    public function setDefaultCurrency(string $currency): void
    {
        if ($this->isSupported($currency)) {
            $this->defaultCurrency = $currency;
        }
    }

    /**
     * Get decimal places for currency.
     */
    public function getDecimalPlaces(string $currency = null): int
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        
        return $this->supportedCurrencies[$currency]['decimal_places'] ?? 2;
    }

    /**
     * Round amount to currency decimal places.
     */
    public function round(float $amount, string $currency = null): float
    {
        $decimalPlaces = $this->getDecimalPlaces($currency);
        
        return round($amount, $decimalPlaces);
    }

    /**
     * Format amount with currency symbol only.
     */
    public function formatSimple(float $amount, string $currency = null): string
    {
        $currency = $currency ?: $this->getDefaultCurrency();
        $symbol = $this->getSymbol($currency);
        $formatted = number_format($amount, $this->getDecimalPlaces($currency));
        
        return $symbol . $formatted;
    }
}

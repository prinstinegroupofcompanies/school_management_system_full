<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string format(float $amount, string $currency = null, string $locale = null)
 * @method static string getSymbol(string $currency = null)
 * @method static string getCode(string $currency = null)
 * @method static string getName(string $currency = null)
 * @method static array getSupportedCurrencies()
 * @method static bool isSupported(string $currency)
 * @method static float convert(float $amount, string $from, string $to)
 * @method static float getExchangeRate(string $from, string $to)
 * @method static string getDefaultCurrency()
 * @method static void setDefaultCurrency(string $currency)
 * 
 * @see \App\Services\CurrencyService
 */
class Currency extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'currency';
    }
}

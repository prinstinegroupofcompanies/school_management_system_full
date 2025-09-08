<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed config(string $key, mixed $default = null)
 * @method static string getCurrency()
 * @method static string getCurrencySymbol()
 * @method static string getAcademicYear()
 * @method static int getMaxFileSize()
 * @method static array getAllowedFileTypes()
 * @method static bool isBackupEnabled()
 * @method static int getBackupRetentionDays()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static bool has(string $key)
 * @method static array all()
 * @method static void forget(string $key)
 * @method static void flush()
 * 
 * @see \App\Services\SchoolService
 */
class School extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'school';
    }
}

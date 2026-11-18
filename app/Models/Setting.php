<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static ?array $cache = null;

    /** One saved setting, or the default when it was never saved. */
    public static function get(string $key, $default = null)
    {
        if (self::$cache === null) {
            self::$cache = [];

            try {
                if (Schema::hasTable('settings')) {
                    self::$cache = static::query()->pluck('value', 'key')->all();
                }
            } catch (\Throwable $e) {
                // Table not ready yet: defaults are used.
            }
        }

        return self::$cache[$key] ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        self::$cache = null;
    }

    /** School year like "2026-2027". Defaults to the one that starts this June. */
    public static function schoolYear(): string
    {
        $saved = static::get('school_year');

        if ($saved) {
            return $saved;
        }

        $year = now()->month >= 6 ? now()->year : now()->year - 1;

        return $year . '-' . ($year + 1);
    }

    public static function schoolName(): string
    {
        return static::get('school_name') ?: 'Ateneo de Zamboanga University — Senior High School';
    }

    public static function enrollmentOpen(): bool
    {
        return static::get('enrollment_open', '1') === '1';
    }

    public static function closedMessage(): string
    {
        return static::get('closed_message') ?: 'Online enrollment is closed for now. Please visit the Admissions Office.';
    }
}

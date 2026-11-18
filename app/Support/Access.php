<?php

namespace App\Support;

use App\Models\RolePermission;
use Illuminate\Support\Facades\Schema;

/**
 * Who can open and change what.
 *
 * Offices (roles): admin, admission, infirmary, id_capturing, gco.
 * Every office gets one level for each module: none, view (look only) or edit.
 * The Administrator always has full access, plus the system pages
 * (Manage Account, Access Rights, Settings), which nobody else can open.
 */
class Access
{
    public const ADMIN = 'admin';

    /** Offices (everyone except the Administrator). */
    public const OFFICES = [
        'admission' => 'Admission Office',
        'infirmary' => 'Infirmary',
        'id_capturing' => 'ID Capturing Office',
        'gco' => 'GCO (Guidance Office)',
    ];

    public const LEVELS = [
        'none' => 'No access',
        'view' => 'View only',
        'edit' => 'Can edit',
    ];

    /** Module key => label shown in the Access Rights page. */
    public const MODULES = [
        'dashboard' => 'Dashboard',
        'step1' => 'Step 1 · Enrollment',
        'step2' => 'Step 2 · Infirmary',
        'step3' => 'Step 3 · Admission',
        'step4' => 'Step 4 · ID Capturing',
        'step5' => 'Step 5 · GCO Assessment',
        'step6' => 'Step 6 · Enrolled',
        'students' => 'Student Directory',
        'capacity' => 'Capacity Control',
        'reports' => 'Reports',
    ];

    /** Page a person lands on first, in this order, for the first module they can view. */
    public const LANDING = [
        'dashboard' => 'admin.dashboard',
        'step1' => 'admin.index',
        'step2' => 'admin.step2.index',
        'step3' => 'admin.step3.index',
        'step4' => 'admin.step4.index',
        'step5' => 'admin.step5.index',
        'step6' => 'admin.step6.index',
        'students' => 'admin.students.index',
        'capacity' => 'admin.capacity.index',
        'reports' => 'admin.reports.index',
    ];

    /** Starting access for each office. The Administrator can change these. */
    public const DEFAULTS = [
        'admission' => [
            'dashboard' => 'view',
            'step1' => 'edit',
            'step2' => 'view',
            'step3' => 'edit',
            'step4' => 'view',
            'step6' => 'view',
            'students' => 'view',
            'capacity' => 'edit',
            'reports' => 'view',
        ],
        'infirmary' => [
            'step2' => 'edit',
        ],
        'id_capturing' => [
            'step4' => 'edit',
        ],
        'gco' => [
            'step5' => 'edit',
        ],
    ];

    private static ?array $cache = null;

    /** All roles with their label. */
    public static function roles(): array
    {
        return ['admin' => 'Administrator'] + self::OFFICES;
    }

    /** [role => [module => level]] as saved, falling back to the defaults. */
    public static function matrix(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $saved = [];

        try {
            if (Schema::hasTable('role_permissions')) {
                foreach (RolePermission::all() as $row) {
                    $saved[$row->role][$row->module] = $row->level;
                }
            }
        } catch (\Throwable $e) {
            // Table not ready yet: the defaults are used.
        }

        $matrix = [];

        foreach (array_keys(self::OFFICES) as $role) {
            foreach (array_keys(self::MODULES) as $module) {
                $matrix[$role][$module] = $saved[$role][$module]
                    ?? (self::DEFAULTS[$role][$module] ?? 'none');
            }
        }

        return self::$cache = $matrix;
    }

    /** none, view or edit for one role and module. */
    public static function level(?string $role, string $module): string
    {
        $role = $role ?: self::ADMIN;

        if ($role === self::ADMIN) {
            return 'edit';
        }

        // System pages are for the Administrator only.
        if ($module === 'system') {
            return 'none';
        }

        return self::matrix()[$role][$module] ?? 'none';
    }

    /** Forget what was loaded, after the rights were changed. */
    public static function forget(): void
    {
        self::$cache = null;
    }

    /** Write the starting access back to the database. */
    public static function resetToDefaults(): void
    {
        RolePermission::query()->delete();

        foreach (array_keys(self::OFFICES) as $role) {
            foreach (array_keys(self::MODULES) as $module) {
                RolePermission::create([
                    'role' => $role,
                    'module' => $module,
                    'level' => self::DEFAULTS[$role][$module] ?? 'none',
                ]);
            }
        }

        self::forget();
    }
}

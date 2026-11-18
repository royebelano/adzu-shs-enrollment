<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\User;
use App\Support\Access;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Access Rights: what each office may view or edit. */
class AccessController extends Controller
{
    public function index()
    {
        return view('admin.access.index', [
            'matrix' => Access::matrix(),
            'offices' => Access::OFFICES,
            'modules' => Access::MODULES,
            'levels' => Access::LEVELS,
            'people' => User::query()->get(['role'])->groupBy(fn ($u) => $u->roleKey())->map->count(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'access' => 'required|array',
            'access.*.*' => ['required', Rule::in(array_keys(Access::LEVELS))],
        ]);

        foreach (array_keys(Access::OFFICES) as $role) {
            foreach (array_keys(Access::MODULES) as $module) {
                RolePermission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    ['level' => $validated['access'][$role][$module] ?? 'none']
                );
            }
        }

        Access::forget();

        return back()->with('success', 'Access rights saved. They apply the next time each person opens a page.');
    }

    public function reset()
    {
        Access::resetToDefaults();

        return back()->with('success', 'Access rights were set back to the starting setup.');
    }
}

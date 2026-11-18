<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Access;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Manage Account: create and look after the staff accounts of each office. */
class AccountController extends Controller
{
    /** Active Administrators other than this one. */
    private function otherActiveAdmins(User $user): int
    {
        return User::query()
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->where(fn ($q) => $q->where('role', Access::ADMIN)->orWhereNull('role'))
            ->count();
    }

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->input('q');
                $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when(array_key_exists((string) $request->input('role'), Access::roles()), fn ($q) => $q->where('role', $request->input('role')))
            ->orderBy('name')
            ->get();

        $counts = User::query()->get(['role', 'is_active'])->groupBy(fn ($u) => $u->roleKey())->map->count();

        return view('admin.accounts.index', [
            'users' => $users,
            'roles' => Access::roles(),
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(array_keys(Access::roles()))],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'That email already has an account.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Account created for ' . $validated['name'] . '. Share the password with them in person.');
    }

    public function edit(User $user)
    {
        return view('admin.accounts.edit', [
            'account' => $user,
            'roles' => Access::roles(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(Access::roles()))],
        ], [
            'email.unique' => 'That email already has an account.',
        ]);

        // Keep at least one Administrator who can still sign in.
        if ($user->isAdmin() && $validated['role'] !== Access::ADMIN && $this->otherActiveAdmins($user) === 0) {
            return back()->withInput()->withErrors([
                'role' => 'This is the only active Administrator. Make another account an Administrator first.',
            ]);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Account updated.');
    }

    public function password(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $user->update(['password' => $validated['password']]);

        return back()->with('success', 'Password changed for ' . $user->name . '.');
    }

    public function toggle(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        $disabling = $user->is_active !== false;

        if ($disabling && $user->isAdmin() && $this->otherActiveAdmins($user) === 0) {
            return back()->with('error', 'This is the only active Administrator, so it cannot be disabled.');
        }

        $user->update(['is_active' => !$disabling]);

        return back()->with('success', $user->name . ($disabling ? ' was disabled and can no longer sign in.' : ' is active again.'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && $this->otherActiveAdmins($user) === 0) {
            return back()->with('error', 'This is the only active Administrator, so it cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', $name . ' was deleted.');
    }
}

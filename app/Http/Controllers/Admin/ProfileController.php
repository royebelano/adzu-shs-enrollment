<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/** My account: every signed-in person can change their own name and password. */
class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.profile.index', ['account' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'email.unique' => 'That email already has an account.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Your details were saved.');
    }

    public function password(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'The new password must be at least 8 characters.',
            'password.confirmed' => 'The two new passwords do not match.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is not correct.']);
        }

        $user->update(['password' => $validated['password']]);

        return back()->with('success', 'Your password was changed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/** Settings: details the whole system uses, such as the school year. */
class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'schoolName' => Setting::schoolName(),
            'schoolYear' => Setting::schoolYear(),
            'enrollmentOpen' => Setting::enrollmentOpen(),
            'closedMessage' => Setting::get('closed_message', ''),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|string|max:120',
            'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'closed_message' => 'nullable|string|max:200',
        ], [
            'school_year.regex' => 'Write the school year like 2026-2027.',
        ]);

        [$from, $to] = array_map('intval', explode('-', $validated['school_year']));

        if ($to !== $from + 1) {
            return back()->withInput()->withErrors([
                'school_year' => 'The second year must come right after the first, like 2026-2027.',
            ]);
        }

        Setting::put('school_name', $validated['school_name']);
        Setting::put('school_year', $validated['school_year']);
        Setting::put('enrollment_open', $request->boolean('enrollment_open') ? '1' : '0');
        Setting::put('closed_message', $validated['closed_message'] ?? '');

        return back()->with('success', 'Settings saved.');
    }
}

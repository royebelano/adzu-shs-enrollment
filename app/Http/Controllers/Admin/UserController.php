<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** Pamamahala ng mga admin account (lahat ng user sa sistemang ito ay admin). */
class UserController extends Controller
{
    public function index()
    {
        return view('admin.users', ['users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'Ilagay ang pangalan.',
            'email.required'     => 'Ilagay ang email.',
            'email.email'        => 'Hindi tamang email.',
            'email.unique'       => 'May admin na gumagamit ng email na ito.',
            'password.required'  => 'Ilagay ang password.',
            'password.min'       => 'Hindi bababa sa 8 characters ang password.',
            'password.confirmed' => 'Hindi magkapareho ang dalawang password.',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Nagawa na ang bagong admin account.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Ilagay ang bagong password.',
            'password.min'       => 'Hindi bababa sa 8 characters ang password.',
            'password.confirmed' => 'Hindi magkapareho ang dalawang password.',
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', "Napalitan na ang password ni {$user->name}.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Hindi mo mabubura ang sarili mong account.']);
        }
        if (User::count() <= 1) {
            return back()->withErrors(['user' => 'Kailangang may kahit isang admin.']);
        }

        $user->delete();

        return back()->with('status', 'Nabura na ang admin account.');
    }
}

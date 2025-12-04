<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'customer') {
            return back()->withErrors(['impersonate' => 'Only customer accounts can be impersonated.']);
        }

        $request->session()->put('impersonator_id', $request->user()->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'You are now logged in as '.$user->email.'.');
    }

    public function stop(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if ($impersonatorId) {
            $admin = User::find($impersonatorId);
            if ($admin) {
                Auth::login($admin);
                $request->session()->regenerate();

                return redirect()->route('dashboard')->with('status', 'Returned to your admin account.');
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

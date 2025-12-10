<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.password');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'whatsapp' => ['nullable', 'string', 'max:30'],
        ]);

        $user = $request->user();

        if ($request->filled('password')) {
            if (! $request->filled('current_password')) {
                return back()->withErrors(['current_password' => 'Please enter your current password to change it.']);
            }
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->has('whatsapp')) {
            $user->whatsapp = preg_replace('/[\\s\\-()]/', '', (string) $request->input('whatsapp'));
        }

        $user->save();

        return back()->with('status', 'Details updated successfully.');
    }
}

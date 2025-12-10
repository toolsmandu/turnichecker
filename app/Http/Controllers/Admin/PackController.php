<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\User;
use App\Models\UserPack;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PackController extends Controller
{
    public function index(): View
    {
        return view('admin.packs', [
            'packs' => Pack::latest()->get(),
            'users' => User::where('role', 'customer')->orderBy('email')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quota' => ['required', 'integer', 'min:1'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        Pack::create([
            'name' => $data['name'],
            'quota' => $data['quota'],
            'duration_days' => $data['duration_days'],
        ]);

        return back()->with('status', 'Pack created.');
    }

    public function update(Request $request, Pack $pack): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quota' => ['required', 'integer', 'min:1'],
            'duration_days' => ['required', 'integer', 'min:1'],
        ]);

        $pack->update([
            'name' => $data['name'],
            'quota' => $data['quota'],
            'duration_days' => $data['duration_days'],
        ]);

        return back()->with('status', 'Pack updated.');
    }

    public function destroy(Pack $pack): RedirectResponse
    {
        $pack->delete();

        return back()->with('status', 'Pack deleted.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'pack_id' => ['required', 'exists:packs,id'],
        ]);

        $pack = Pack::findOrFail($data['pack_id']);

        UserPack::assignPack($data['user_id'], $pack);

        return back()->with('status', 'Pack assigned to user.');
    }

    public function createCustomer(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'pack_id' => ['nullable', 'exists:packs,id'],
        ]);

        $sanitizedWhatsapp = preg_replace('/[\\s\\-()]/', '', $data['whatsapp']);

        $user = User::create([
            'email' => $data['email'],
            'name' => $data['name'] ?? strstr($data['email'], '@', true),
            'whatsapp' => $sanitizedWhatsapp,
            'password' => Hash::make('paper@123'),
            'role' => 'customer',
        ]);

        if (!empty($data['pack_id'])) {
            $pack = Pack::findOrFail($data['pack_id']);
            UserPack::assignPack($user->id, $pack);
        }

        return back()->with([
            'status' => 'Customer created with default password "paper@123".',
            'new_customer' => [
                'email' => $user->email,
                'password' => 'paper@123',
                'login_url' => 'https://turnichecker.online/login',
            ],
        ]);
    }
}

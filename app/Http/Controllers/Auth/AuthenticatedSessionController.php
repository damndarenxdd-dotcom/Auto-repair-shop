<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private function ensureDefaultUsersExist(): void
    {
        if (User::count() > 0) {
            return;
        }

        $defaults = [
            ['name' => 'Admin User', 'email' => 'admin@autorepairshop.test', 'password' => 'password', 'role_id' => 1],
            ['name' => 'John Manager', 'email' => 'manager@autorepairshop.test', 'password' => 'password', 'role_id' => 2],
            ['name' => 'Mike Mechanic', 'email' => 'mechanic1@autorepairshop.test', 'password' => 'password', 'role_id' => 3],
            ['name' => 'Tom Technician', 'email' => 'mechanic2@autorepairshop.test', 'password' => 'password', 'role_id' => 3],
            ['name' => 'Alice Johnson', 'email' => 'customer1@autorepairshop.test', 'password' => 'password', 'role_id' => 4],
            ['name' => 'Bob Smith', 'email' => 'customer2@autorepairshop.test', 'password' => 'password', 'role_id' => 4],
            ['name' => 'Carol Williams', 'email' => 'customer3@autorepairshop.test', 'password' => 'password', 'role_id' => 4],
        ];

        foreach ($defaults as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make($user['password']),
                    'email_verified_at' => now(),
                    'role_id' => $user['role_id'],
                    'phone' => '555-0000',
                    'address' => 'Default system address',
                ]
            );
        }
    }

    public function create(): View
    {
        $this->ensureDefaultUsersExist();

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureDefaultUsersExist();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

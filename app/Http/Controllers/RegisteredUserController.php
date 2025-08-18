<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Facades\Tenancy;

class RegisteredUserController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login in central database
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Find the tenant for this user
            $tenant = Tenant::where('email', $request->email)->first();
            if ($tenant) {
                return redirect()->away('http://' . $tenant->domains->first()->domain);
            }

            // If no tenant found, just redirect home
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email', // central users table
            'password' => 'required|string|min:8|confirmed',
            'subdomain' => 'required|string|alpha_dash|min:3|unique:domains,domain',
        ]);

        // Create tenant in the central database
        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Create the tenant's domain
        $tenant->domains()->create(['domain' => $request->subdomain . config('session.domain')]);

        // Create the initial user in the **central users table**, not tenant DB
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        tenancy()->initialize($tenant);  // switch to tenant DB

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log in the new user with tenant db
        Auth::login($user);

        return redirect()->to('http://' . $tenant->domains->first()->domain);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(config('app.url'));
    }
}

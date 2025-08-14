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

        // Find tenant by email
        $tenant = Tenant::where('email', $request->email)->first();
        $maindomain  = config('session.domain');

        if ($tenant) {
            // Initialize tenant
            Tenancy::initialize($tenant);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();

                return redirect()->away('http://' . $tenant->domains->first()->domain.$maindomain);

            }
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenants,email',
            'password' => 'required|string|min:8|confirmed',
            'subdomain' => 'required|string|alpha_dash|min:3|unique:domains,domain',
        ]);

        // Create tenant in the central database
        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Create the new tenant's database and run migrations
        $tenant->domains()->create(['domain' => $request->subdomain]);
        Tenancy::initialize($tenant);

        // Create the initial user in the tenant's new database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log in the new user and redirect
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

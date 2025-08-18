<?php

declare(strict_types=1);

use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/


Route::middleware([
    InitializeTenancyByDomain::class,
    'web',
    PreventAccessFromCentralDomains::class,
])->group(function () {

    config(['session.cookie' => 'tenant_' . tenant('id') . '_session']);

    // Tenant login routes — no auth middleware
    Route::get('/login', [RegisteredUserController::class, 'tenantLoginForm'])->name('tenant.login');
    Route::post('/login', [RegisteredUserController::class, 'tenantLogin'])->name('tenant.login.post');
});

Route::middleware([
    InitializeTenancyByDomain::class,
    'web',
    PreventAccessFromCentralDomains::class,
    'auth:tenant', // only protect these routes
])->group(function () {
    Route::get('/logout', [RegisteredUserController::class, 'logout'])->name('tenant.logout');
    Route::get('/', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
});

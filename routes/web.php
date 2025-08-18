<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisteredUserController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



Route::middleware(['web',])
    ->domain('tenant-laravel.com')
    ->group(function () {
        Route::get('/', function () {
            return view('auth.register');
        });

        Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');


        Route::get('/login', [RegisteredUserController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [RegisteredUserController::class, 'login'])->name('login.post');
});

// Route::middleware(['web', 'auth', 'tenancy'])
//     ->domain('{tenant}.tenant-laravel.com')
//     ->group(function () {
//         Route::get('/', [PostController::class, 'index'])->name('posts.index');
//         Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
//         Route::get('/logout', [RegisteredUserController::class, 'logout'])->name('logout');
//     });

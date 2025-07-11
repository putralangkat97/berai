<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Berai\DashboardController;
use App\Http\Controllers\Berai\ProjectController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('index');
        });

        // auth
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredUserController::class, 'store']);
    });

Route::middleware('auth')
    ->group(function () {
        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // project
        Route::prefix('/project')
            ->name('project.')
            ->group(function () {
                Route::get('/', [ProjectController::class, 'index'])->name('index');
                Route::get('/create', [ProjectController::class, 'create'])->name('create');
                Route::post('/create', [ProjectController::class, 'store'])->name('store');
                Route::get('/show/{project}', [ProjectController::class, 'show'])->name('show');
            });

        // logout
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    });

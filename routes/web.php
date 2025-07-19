<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Berai\CommentController;
use App\Http\Controllers\Berai\DashboardController;
use App\Http\Controllers\Berai\ProjectController;
use App\Http\Controllers\Berai\ProjectMemberController;
use App\Http\Controllers\Berai\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return Inertia::render('index');
    });

    // auth
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // welcome
    Route::get('/welcome', [DashboardController::class, 'welcome'])->name('welcome');

    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // project
    Route::prefix('/project')
        ->name('project.')
        ->group(function () {
            // Project
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/create', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');

            // Project Member
            Route::post('/{project}/members', ProjectMemberController::class);
        });

    // task
    Route::prefix('/task')
        ->name('task.')
        ->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::post('/{project}', [TaskController::class, 'store'])->name('store');
            Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

            // comment
            Route::post('/{task}/comments', [CommentController::class, 'store'])->name('comment');
        });

    // profile
    Route::prefix('/profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::post('/', [ProfileController::class, 'update'])->name('update');
        });

    // logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

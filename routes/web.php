<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use \App\Livewire\Admin\Home\Index;

Route::get('/', Index::class)->name('home');

Route::get('/submit', \App\Livewire\Admin\Support\Index::class);
Route::get('/aaa', \App\Livewire\Chat::class);
// Login
Route::get('/login', fn() => view('login'))->name('login');
Route::get('/view-user-chats',\App\Livewire\Home\UserChats::class);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Register
Route::get('/register', fn() => view('register'))->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Dashboard
Route::get('/dashboard', Index::class)->middleware('auth');

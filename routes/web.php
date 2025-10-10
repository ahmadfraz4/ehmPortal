<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/users', [UserController::class, 'index'])->name('users.all');
Route::post('chat', [ChatController::class, 'openChat'])->name('open.chat');
Route::get('room/{id}', [ChatController::class, 'chat'])->name('room.chat');
// Route::post('/chat/send', [ChatController::class, 'sendChat'])->name('chat.send');
Route::post('/send-chat', [ChatController::class, 'sendChat'])->middleware('auth')->name('send.chat');

// Route::middleware('')->get('chat/{id}', [ChatController::class, 'single_chat'])->name('room.user');
// Route::get()
// Route::get('users/{id}', function ($id) {
    
// });

require __DIR__.'/auth.php';

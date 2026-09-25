<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Notes routes
    Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/create', [NoteController::class, 'create'])->name('notes.create');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Reminders routes
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('/reminders/create', [ReminderController::class, 'create'])->name('reminders.create');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::get('/reminders/{reminder}/edit', [ReminderController::class, 'edit'])->name('reminders.edit');
    Route::put('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::patch('/reminders/{reminder}/toggle', [ReminderController::class, 'toggleDone'])->name('reminders.toggle');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    // Trash routes
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::patch('/trash/notes/{id}/restore', [TrashController::class, 'restoreNote'])->name('trash.notes.restore');
    Route::delete('/trash/notes/{id}', [TrashController::class, 'forceDeleteNote'])->name('trash.notes.force-delete');
    Route::patch('/trash/reminders/{id}/restore', [TrashController::class, 'restoreReminder'])->name('trash.reminders.restore');
    Route::delete('/trash/reminders/{id}', [TrashController::class, 'forceDeleteReminder'])->name('trash.reminders.force-delete');
});




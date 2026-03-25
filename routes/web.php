<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class , 'index'])->name('home');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login'])->name('login.post');
    Route::get('/register', [AuthController::class , 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class , 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class , 'logout'])->name('logout')->middleware('auth');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class , 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class , 'callback'])->name('auth.google.callback');

// Venues (public browsing)
Route::get('/venues', [VenueController::class , 'index'])->name('venues.index');
Route::get('/venues/{venue}', [VenueController::class , 'show'])->name('venues.show');

// Events (public browsing)
Route::get('/events', [EventController::class , 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class , 'show'])->name('events.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class , 'dashboard'])->name('dashboard');

    // Venues management
    Route::get('/venues/create', [VenueController::class , 'create'])->name('venues.create');
    Route::post('/venues', [VenueController::class , 'store'])->name('venues.store');
    Route::get('/venues/{venue}/edit', [VenueController::class , 'edit'])->name('venues.edit');
    Route::put('/venues/{venue}', [VenueController::class , 'update'])->name('venues.update');
    Route::delete('/venues/{venue}', [VenueController::class , 'destroy'])->name('venues.destroy');
    Route::post('/venues/{venue}/book', [VenueController::class , 'book'])->name('venues.book');
    Route::get('/my-venues', [VenueController::class , 'myVenues'])->name('venues.my');

    // Events management
    Route::get('/events/create', [EventController::class , 'create'])->name('events.create');
    Route::post('/events', [EventController::class , 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class , 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class , 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class , 'destroy'])->name('events.destroy');
    Route::get('/my-events', [EventController::class , 'myEvents'])->name('events.my');

    // Bookings
    Route::get('/bookings', [BookingController::class , 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class , 'show'])->name('bookings.show');
    Route::get('/events/{event}/book', [BookingController::class , 'createEventBooking'])->name('bookings.event.create');
    Route::post('/events/{event}/book', [BookingController::class , 'storeEventBooking'])->name('bookings.event.store');
    Route::post('/bookings/{booking}/cancel', [BookingController::class , 'cancel'])->name('bookings.cancel');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class , 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class , 'users'])->name('users');
    Route::put('/users/{user}/role', [AdminController::class , 'updateUserRole'])->name('users.role');
    Route::get('/venues', [AdminController::class , 'venues'])->name('venues');
    Route::post('/venues/{venue}/approve', [AdminController::class , 'approveVenue'])->name('venues.approve');
    Route::post('/venues/{venue}/reject', [AdminController::class , 'rejectVenue'])->name('venues.reject');
    Route::get('/bookings', [AdminController::class , 'bookings'])->name('bookings');
    Route::get('/events', [AdminController::class , 'events'])->name('events');
});

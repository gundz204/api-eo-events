<?php

use App\Http\Controllers\AuthViewController;
use App\Http\Controllers\EventViewController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [AuthViewController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthViewController::class, 'register']);

Route::get('/login', [AuthViewController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthViewController::class, 'login']);

Route::post('/logout', [AuthViewController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/events', [EventViewController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [EventViewController::class, 'show'])->name('events.show');
    Route::get('/events/{id}/register', [EventViewController::class, 'showRegisterForm'])->name('events.register.form');
    Route::post('/events/{id}/register', [EventViewController::class, 'register'])->name('events.register');
    Route::get('/my-events', [EventViewController::class, 'myEventsView'])->name('events.my_events');
    Route::get('/my-events-registered', [EventViewController::class, 'myRegisteredEvents'])->name('events.my_events_registered');

    Route::middleware('admin')->group(function () {
        Route::get('/events/create', [EventViewController::class, 'create'])->name('events.create');
        Route::post('/events', [EventViewController::class, 'store'])->name('events.store');
    });
});

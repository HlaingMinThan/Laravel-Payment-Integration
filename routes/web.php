<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return auth()->user()->subscriptions;
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//subscribe page
Route::get('/subscribe', function () {
    return view('subscribe', [
        'intent' => auth()->user()->createSetupIntent() //coming from cashier billable trait
    ]);
})->middleware(['auth', 'verified'])->name('subscribe');

Route::post('/subscribe', function () {
    //create subscription
    auth()->user()->newSubscription('My Product', request('plan'))->create(request('paymentMethod'));
    return back();
})->middleware(['auth', 'verified'])->name('subscribe');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

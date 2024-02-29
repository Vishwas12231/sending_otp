<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Registration Routes
Route::get('/registration', [RegisterController::class, 'showRegistrationForm'])->name('registration');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::get('/verify-otp', [RegisterController::class, 'showOtpVerificationForm']);
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('verify-otp');
// Auth::routes();

// // Additional route for OTP verification
// Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('verify-otp');

<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Route\RouterController;
use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('pages.auth.LoginPage');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::middleware(['auth'])->group(function () {

    Route::group(['middleware' => ['role:Super_Admin']], function () {
        Route::get('/', [RouterController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [RouterController::class, 'students'])->name('students');

    });
});

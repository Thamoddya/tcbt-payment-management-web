<?php

use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('pages.auth.LoginPage');
})->name('login');

Route::middleware(['auth'])->group(function () {


    Route::group(['middleware' => ['role:Super_Admin']], function () {

        Route::get('/', function () {
            return view('pages.home.HomePage');
        })->name('superAdmin.dashboard');
    });
});

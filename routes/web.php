<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Route\RouterController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::get('/login', function () {
    return view('pages.auth.LoginPage');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::middleware(['auth'])->group(function () {

    Route::group(['middleware' => ['role:Super_Admin']], function () {

        Route::post('/add-cashier', [Controller::class, 'addCashier'])->name('add.cashier');
        Route::get('/get-cashier/{id}', [Controller::class, 'getCashiers'])->name('get.cashier');
        Route::post('/update-cashier/{id}', [Controller::class, 'updateCashier'])->name('update.cashier');

        Route::post('/reports/generate', [Controller::class, 'generateReport'])->name('reports.generate');

    });
    Route::get('/students', [RouterController::class, 'students'])->name('students');
    Route::get('/cashier', [RouterController::class, 'cashier'])->name('cashier');
    Route::get('/books', [RouterController::class, 'Books'])->name('books');
    Route::get('/reports', [RouterController::class, 'Reports'])->name('reports');
    Route::get('/', [RouterController::class, 'dashboard'])->name('dashboard');
    Route::get('/add-student-payment', [RouterController::class, 'addStudentPayment'])->name('add.payment');

    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::post('/student/update', [StudentController::class, 'update'])->name('students.update');

    Route::get('/students/details/{tcbt_student_number}', [StudentController::class, 'getStudentDetails']);
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::post('/payments/add', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/getPayment/{id}', [PaymentController::class, 'getPaymentByID']);
    Route::post('/updatePayment', [PaymentController::class, 'update']);
    Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

//    BOOK
    Route::post('/books/store', [\App\Http\Controllers\BookController::class, 'store'])->name('books.store');
    Route::post('/books/update/{id}', [\App\Http\Controllers\BookController::class, 'update'])->name('books.update');
    Route::delete('/books/delete/{id}', [\App\Http\Controllers\BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{id}/make-available', [\App\Http\Controllers\BookController::class, 'makeAvailable'])->name('books.makeAvailable');



    Route::get('/library-out', [\App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
    Route::post('/library-borrow', [\App\Http\Controllers\LibraryController::class, 'borrow'])->name('library.borrow');
    Route::put('/library-return/{id}', [\App\Http\Controllers\LibraryController::class, 'returnBook'])->name('library.return');
    Route::get('/library-history', [\App\Http\Controllers\LibraryController::class, 'history'])->name('library.history');


    //Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

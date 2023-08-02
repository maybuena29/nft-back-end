<?php

use App\Http\Controllers\EmployeeCTRL;
use App\Http\Controllers\LoginCTRL;
use App\Http\Controllers\PositionCTRL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [LoginCTRL::class, 'register_user'])->middleware('verify_api_key')->name('register_user');
Route::post('login', [LoginCTRL::class, 'login'])->middleware('verify_api_key');

Route::get('show/user',[LoginCTRL::class,'user_profile'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('user_profile');

Route::get('auth/user',[LoginCTRL::class,'verifyAuth'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('verifyAuth');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth')->group(function(){
//     //Employee Routes
//     Route::get('show/employees',[EmployeeCTRL::class,'index'])->name('index');
//     Route::get('view/employee',[EmployeeCTRL::class,'showEmployee'])->name('showEmployee');
//     Route::post('add/employee',[EmployeeCTRL::class,'storeEmployee'])->name('storeEmployee');
//     Route::put('update/employee',[EmployeeCTRL::class,'updateEmployee'])->name('updateEmployee');
//     Route::delete('archive/employee',[EmployeeCTRL::class,'destroyEmployee'])->name('destroyEmployee');
//     Route::get('employee_archived',[EmployeeCTRL::class,'ArchivedEmployees'])->name('ArchivedEmployees');
//     // Route::resource('employee', EmployeeCTRL::class);

//     Route::get('position_archived',[PositionCTRL::class,'ArchivedPosition'])->name('ArchivedPosition');
//     Route::resource('positions', PositionCTRL::class);
// });



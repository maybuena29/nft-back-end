<?php

use App\Http\Controllers\EmployeeCTRL;
use App\Http\Controllers\LoginCTRL;
use App\Http\Controllers\RoleCTRL;
use Illuminate\Support\Facades\Route;

// Login
Route::post('login', [LoginCTRL::class, 'login'])->middleware('verify_api_key');

// Show All Users
Route::get('user/show/all',[EmployeeCTRL::class,'showUsers'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('showUsers');

// Register Account
Route::post('user/register', [EmployeeCTRL::class, 'registerAccountProfile'])
    ->middleware('verify_api_key')
    ->name('registerAccountProfile');

// Show User Profile
Route::get('user/show/profile',[EmployeeCTRL::class,'showProfile'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('showProfile');

// Show Selected User Profile
Route::get('user/show/profile/{id}',[EmployeeCTRL::class,'showSelectedUser'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('showSelectedUser');

// Update User
Route::put('user/update/{id}',[EmployeeCTRL::class,'updateProfile'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('updateProfile');

// Update User Password
Route::put('user/change/password/{id}',[EmployeeCTRL::class,'userPasswordChange'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('userPasswordChange');

// Archive User
Route::delete('user/archive/{id}',[EmployeeCTRL::class,'archiveUser'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('archiveUser');

// Restore User
Route::post('user/restore/{id}',[EmployeeCTRL::class,'restoreUser'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('restoreUser');

// Show Archived Users
Route::get('user/show/archived',[EmployeeCTRL::class,'showArchivedUsers'])
    ->middleware('verify_api_key', 'verify_token_key')
    ->name('showArchivedUsers');

// Show Roles
Route::get('role/show/all',[RoleCTRL::class,'showRoles'])
    // ->middleware('verify_api_key', 'verify_token_key')
    ->name('showRoles');

















// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

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



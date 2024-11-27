<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('jwt.auth', 'role:super_admin')->group(function () {
        // Rute yang memerlukan autentikasi
        Route::resource('companies', CompanyController::class);
        // Route::apiResource('companies', CompanyController::class);
    });


    // Route::middleware(['jwt.auth', 'role:manager'])->group(function () {
    //     Route::resource('users', UserController::class);
    // });

    Route::middleware(['jwt.auth', 'role:manager'])->group(function () {
        Route::get('managers', [UserController::class, 'getManagers']); // Melihat semua manager
        Route::get('employees', [EmployeeController::class, 'index']); // Melihat semua employee
        Route::get('employees/{id}', [EmployeeController::class, 'show']); // Detail employee
        Route::post('employees', [EmployeeController::class, 'store']); // Tambah employee
        Route::put('employees/{id}', [EmployeeController::class, 'update']); // Update employee
        Route::delete('employees/{id}', [EmployeeController::class, 'destroy']); // Hapus employee
        Route::put('managers/{id}', [UserController::class, 'updateOwnInfo']);
    });

    Route::middleware(['jwt.auth', 'role:employee'])->group(function () {
        Route::get('fellow-employees', [EmployeeController::class, 'getFellowEmployees']); // Semua karyawan di perusahaan yang sama
        Route::get('fellow-employees/{id}', [EmployeeController::class, 'showFellowEmployee']); // Detail karyawan
    });
});

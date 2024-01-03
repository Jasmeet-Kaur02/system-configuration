<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(["middleware" => ['auth:sanctum']], function () {
    Route::put('users/{userId}', [UserController::class, 'update']);
    Route::delete('users/{userId}', [UserController::class, 'delete']);
    Route::post('users/{userId}/profile-picture', [UserController::class, 'updateProfilePicture']);
    Route::delete('users/{userId}/profile-picture', [UserController::class, 'deleteProfilePicture']);

    Route::post('users/{userId}/addresses', [AddressController::class, 'create']);
    Route::put('users/{userId}/addresses/{addressId}', [AddressController::class, 'update']);
    Route::delete('users/{userId}/addresses/{addressId}', [AddressController::class, 'delete']);
});

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"])->name('login');

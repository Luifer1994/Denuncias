<?php

use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::post('/register-user-whistleblower', [UserController::class, 'registerUserWhistleblower']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/rols-active', [RolController::class, 'index']);
    Route::post('/register-user-professional', [UserController::class, 'registerUserProfessional']);
    Route::get('/logout', [UserController::class, 'logout']);
});



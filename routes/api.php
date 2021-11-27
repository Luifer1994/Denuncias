<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::post('/register-user-whistleblower', [UserController::class, 'registerUserWhistleblower']);

Route::group(['middleware' => 'auth:api'], function () {
    //Roles
    Route::get('/rols-active', [RolController::class, 'index']);
    //Usuarios
    Route::post('/register-user-professional', [UserController::class, 'registerUserProfessional']);
    Route::get('/logout', [UserController::class, 'logout']);
    //Denuncias
    Route::get('/complaints-list{limit?}', [ComplaintController::class, 'index']);
    Route::apiResource('/complaints', ComplaintController::class);
});

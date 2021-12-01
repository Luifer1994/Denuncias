<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::post('/register-user-informer', [UserController::class, 'registerUserInformer']);
Route::post('/register-complaint', [ComplaintController::class, 'store']);

Route::group(['middleware' => 'auth:api'], function () {
    //Roles
    Route::get('/rols-active', [RolController::class, 'index']);
    //Usuarios
    Route::post('/register-user-professional', [UserController::class, 'registerUserProfessional']);
    Route::get('/logout', [UserController::class, 'logout']);
    //Denuncias
    Route::get('/complaints-list{search?}{state?}{limit?}{page?}', [ComplaintController::class, 'index']);
    Route::apiResource('/complaints', ComplaintController::class);
    //Usuarios
    Route::get('list-users-informers{search?}{limit?}{page?}', [UserController::class,'ListUserInformers']);
    Route::get('/user-auth', [UserController::class, 'userAuth']);
});

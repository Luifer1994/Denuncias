<?php

use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/rols-active', [RolController::class, 'index']);
});



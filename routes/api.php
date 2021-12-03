<?php

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TypeComplaintController;
use App\Http\Controllers\TypeDocumentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//User
Route::post('/login', [UserController::class, 'login']);
Route::post('/register-user-informer', [UserController::class, 'registerUserInformer']);
//Denuncias
Route::post('/register-complaint', [ComplaintController::class, 'store']);
Route::get('/complaints-by-cod/{cod?}', [ComplaintController::class, 'filterByCode']);
Route::apiResource('/complaint-types', TypeComplaintController::class);
//Respuestas
Route::get('/media-by-response/{id}', [ResponseController::class, 'getMedia']); //Media por respuesta
//Tipos documentos
Route::get('list-type-documents', [TypeDocumentController::class, 'index']);
//Rutas protegidas
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
    Route::get('list-users-informers{search?}{limit?}{page?}', [UserController::class, 'ListUserInformers']);
    Route::get('/user-auth', [UserController::class, 'userAuth']);
    Route::post('/register-official', [UserController::class, 'RegisterOfficial']);
    Route::get('/list-official', [UserController::class, 'ListOfficial']);
    //Respuestas
    Route::post('/response-add', [ResponseController::class, 'store']);
});

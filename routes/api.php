<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TypeComplaintController;
use App\Http\Controllers\TypeDocumentController;
use App\Http\Controllers\TypePeopleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//User
Route::post('/login', [UserController::class, 'login']);
Route::post('/register-user-informer', [UserController::class, 'registerUserInformer']);
Route::post('/send-token-reset-password', [UserController::class, 'sendTokenResetPassword']);
Route::post('/reset-password', [UserController::class, 'recoveryPassword']);
//Denuncias
Route::post('/register-complaint', [ComplaintController::class, 'store']);
Route::get('/complaints-by-cod/{cod?}', [ComplaintController::class, 'filterByCode']);
Route::apiResource('/complaint-types', TypeComplaintController::class);
Route::get('/complaints-export', [ComplaintController::class, 'export']);
//Respuestas
Route::get('/media-by-response/{id}', [ResponseController::class, 'getMedia']); //Media por respuesta
//Tipos documentos
Route::get('list-type-documents', [TypeDocumentController::class, 'index']);
//Tipos personas
Route::get('list-type-peoples', [TypePeopleController::class, 'index']);
//Roles
Route::get('/rols-active', [RolController::class, 'index']);
//Porfesiones
Route::get('/professions-active', [ProfessionController::class, 'index']);
//Ciudades
Route::get('/cities-list', [CityController::class, 'index']);



//Rutas protegidas
Route::group(['middleware' => 'auth:api'], function () {

    //Usuarios
    Route::post('/register-user-professional', [UserController::class, 'registerUserProfessional']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::put('/update-auth', [UserController::class, 'updateAuth']);
    Route::put('/update-password', [UserController::class, 'changePassword']);
    Route::put('/update-official/{id}', [UserController::class, 'UpdateOfficial']);
    Route::get('/validate-sesion', [UserController::class, 'validateSesion']);
    //Denuncias
    Route::get('/complaints-list{search?}{state?}{limit?}{page?}', [ComplaintController::class, 'index']);
    Route::apiResource('/complaints', ComplaintController::class);
    Route::put('/asigne-lawyer/{id}', [ComplaintController::class, 'asigneLawyer']);
    Route::put('/asigne-notify/{id}', [ComplaintController::class, 'asigneNotify']);
    Route::put('/complaint-closed/{id}', [ComplaintController::class, 'closed']);
    Route::put('/complaint-cancel/{id}', [ComplaintController::class, 'cancel']);
    Route::get('/complaints-list-by-user/{search?}{limit?}{page?}', [ComplaintController::class, 'listByUser']);
    //Usuarios
    Route::get('list-users-informers{search?}{limit?}{page?}', [UserController::class, 'ListUserInformers']);
    Route::get('list-users-official{search?}{limit?}{page?}', [UserController::class, 'ListUserOfficial']);
    Route::get('/user-auth', [UserController::class, 'userAuth']);
    Route::post('/register-official', [UserController::class, 'RegisterOfficial']);
    Route::get('/list-official', [UserController::class, 'ListOfficial']);
    Route::get('/list-users-lawyer', [UserController::class, 'ListLawyer']);
    Route::get('/filter-user-by-id/{id}', [UserController::class, 'filterById']);
    //Respuestas
    Route::post('/response-add', [ResponseController::class, 'store']);
    Route::put('/complaint-update-proccess/{id}', [ComplaintController::class, 'updateProccess']);
});

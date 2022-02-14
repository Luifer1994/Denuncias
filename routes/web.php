<?php

use App\Http\Controllers\ComplaintController;
use App\Mail\EmailMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/* Route::get('/notify-state-admin', [ComplaintController::class, 'notifyStateAdmin']);
Route::get('/notify-state-funt', [ComplaintController::class, 'notifyStateFunt']); */

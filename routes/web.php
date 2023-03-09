<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/amocrm-api', 'App\Http\Controllers\AmoCRMApi@index')
    ->name('amocrm-api.index');

Route::get('/amocrm-api/get-token', 'App\Http\Controllers\AmoCRMApi@getToken')
    ->name('amocrm-api.get-token');

Route::get('/amocrm-api/get-token2/{code}', 'App\Http\Controllers\AmoCRMApi@getToken2')
    ->name('amocrm-api.get-token2');

Route::get('/lead', 'App\Http\Controllers\Lead@index')
    ->name('lead.index');

Route::get('/lead/fetch/', 'App\Http\Controllers\Lead@fetch')
    ->middleware('amocrm-api')
    ->name('lead.fetch');

Route::get('/', function () {
    return view('welcome');
});

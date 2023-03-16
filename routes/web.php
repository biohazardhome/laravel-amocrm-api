<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmoCRMApi;
use App\Http\Controllers\Lead;

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

Route::prefix('amocrm-api')->name('amocrm-api.')->controller(AmoCRMApi::class)->group(function() {
    Route::get('/', 'index');

    Route::get('/get-token', 'getToken')
        ->name('get-token');

    Route::get('/get-token2/{code}', 'getToken2')
        ->name('get-token2');
});

Route::prefix('lead')->name('lead.')->controller(Lead::class)->group(function() {
    Route::get('/', 'index');

    Route::get('/fetch/', 'fetch')
        ->middleware('amocrm-api')
        ->name('fetch');
});

Route::get('/', function () {
    return view('welcome');
});

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

Route::get('/lead', 'App\Http\Controllers\Lead@index')->name('lead.index');
Route::get('/lead/fetch/', 'App\Http\Controllers\Lead@fetch')->name('lead.fetch');

Route::get('/', function () {
    return view('welcome');
});

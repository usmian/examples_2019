<?php

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
Route::get('/', function (){
    return view('registration');
});

/** Чисто для примера сделан проход команды регистрации */
Route::prefix('auth')->group(function () {
    Route::post('registration', 'Auth\RegisterController@registration'); /** @see \App\Http\Controllers\RegisterController::registration() */
});


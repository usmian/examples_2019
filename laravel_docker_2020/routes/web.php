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
    return view('welcome');
});

/** Чисто для примера сделан проход команды регистрации */
Route::prefix('auth')->group(function () {
    Route::get('registration', 'RegisterController@getRegistrationForm'); /** @see \App\Http\Controllers\RegisterController::getRegistrationForm() */
    Route::post('registration', 'RegisterController@registration'); /** @see \App\Http\Controllers\RegisterController::registration() */
});


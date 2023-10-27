<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Auth\AuthController@register')->name('register');

Route::post('login', 'Auth\AuthController@login')->name('login');

// protected routes
Route::middleware('jwt.verify')->group(function(){

    Route::get('users' , 'UserController@index')->name('users');

    // Ruta para obtener los datos del usuario actual
    Route::post('me', 'Auth\AuthController@me')->name('me');

    // Ruta para refrescar el token
    Route::get('refresh', 'Auth\AuthController@refresh')->name('refresh');

    // Ruta para cerrar sesión y revocar el token
    Route::post('logout', 'Auth\AuthController@logout')->name('logout');

});

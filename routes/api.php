<?php

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

Route::group([
    'middleware'=>'api',
    'namespace'=>'App\Http\Controllers',
    'prefix'=>'auth'
],function($router){
    route::post('login','AuthController@login');
    route::post('register','AuthController@register');
    route::post('logout','AuthController@logout');
    route::get('profile','AuthController@profile');
    route::post('refresh','AuthController@refresh');
    route::post('otp','AuthController@otp');
    Route::post('reset', 'AuthController@reset_password');

});

Route::group([
    'middleware'=>'api',
    'namespace'=>'App\Http\Controllers',
    'prefix'=>'auth'
],function($router){
    route::resource('todos','TodoController');
});


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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['middleware'=>['auth:api'], 'namespace'=>'Api'], function(){
	Route::resource('roles','RoleController');
	Route::resource('users','UserController');
	Route::get('/varify','UserController@varify');
	Route::post('roles/delete','RoleController@deleteMultiple');
	Route::post('users/delete','UserController@deleteMultiple');
});

Route::post('login','Api\UserController@login')->name('login');

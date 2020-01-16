<?php

use Illuminate\Http\Request;

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


Route::post('/task', 'QueueController@addOrUpdateTask');
Route::put('/task', 'QueueController@addOrUpdateTask');
Route::get('/task', 'QueueController@getTask');
Route::get('/task/{id}', 'QueueController@getTaskById');

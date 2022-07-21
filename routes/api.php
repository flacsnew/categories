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

Route::prefix('categories')->group(function () {
    Route::post('add', 'CategoriesController@add');
    //Route::get('add', 'CategoriesController@add');
    Route::delete('delete', 'CategoriesController@delete');
    //Route::get('delete', 'CategoriesController@delete');
    Route::get('getByID', 'CategoriesController@getByID');
    Route::get('getBySlug', 'CategoriesController@getBySlug');
    Route::put('update', 'CategoriesController@update');
    //Route::get('update', 'CategoriesController@update');
    Route::get('filter', 'CategoriesController@getByFilter');
});
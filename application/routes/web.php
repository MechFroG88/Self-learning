<?php

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

Route::get('/', function () {
    return view('welcome');
});

/**
 * User Route
 */

Route::post('user/login','UserController@login');

Route::middleware('auth')->post('user/logout','UserController@logout');
Route::post('user','UserController@create');
Route::middleware('auth')->get('user','UserController@get_current');
Route::middleware('auth')->post('user/submit','UserController@submit');

Route::middleware('admin')->get('users','UserController@get_all');
Route::middleware('admin')->put('user/{id}','UserController@edit');
Route::middleware('admin')->delete('user/{id}','UserController@delete');

/**
 * Class Route
 */

Route::middleware('admin')->post('class','_ClassController@create');
Route::middleware('admin')->get('class','_ClassController@get');
Route::middleware('admin')->put('class/{id}','_ClassController@edit');
Route::middleware('admin')->delete('class/{id}','_ClassController@delete');

/**
 * Lessons Route
 */

Route::middleware('admin')->post('lesson','LessonController@create');
Route::middleware('admin')->get('lesson','LessonController@get');
Route::middleware('admin')->get('lesson/{id}','LessonController@get_single');
Route::middleware('admin')->put('lesson/{id}','LessonController@edit');
Route::middleware('admin')->delete('lesson/{id}','LessonController@delete');

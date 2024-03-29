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

// Route::get('/', function () {
//     return view('welcome');
// });

/**
 * User Route
 */

//Route::post('hash','UserController@hash');
Route::middleware('admin')->get('cache','UserController@flush_cache');
Route::post('user/login','UserController@login');

Route::middleware('auth')->post('user/logout','UserController@logout');
Route::middleware('auth')->post('user','UserController@create');
Route::middleware('auth')->get('user','UserController@get_current');
Route::middleware('auth')->get('user/lesson','UserController@get_lesson');
Route::middleware('auth')->post('user/submit','UserController@submit');
Route::middleware('auth')->post('user/reselect','UserController@reselect');

Route::middleware('admin')->get('users','UserController@get_all');
Route::middleware('admin')->post('user/edit/{id}','UserController@edit');
Route::middleware('admin')->post('user/edit_ic/{id}','UserController@edit_id');
Route::middleware('admin')->delete('user/{id}','UserController@delete');

/**
 * Class Route
 */

Route::middleware('admin')->post('class','_ClassController@create');
Route::middleware('admin')->get('class','_ClassController@get');
Route::middleware('admin')->post('class/edit/{id}','_ClassController@edit');
Route::middleware('admin')->delete('class/{id}','_ClassController@delete');

/**
 * Lessons Route
 */

Route::middleware('admin')->get('lesson/recount','LessonController@recount');
Route::middleware('admin')->post('lesson','LessonController@create');
Route::middleware('auth')->get('lesson','LessonController@get');
Route::middleware('admin')->get('lessons','LessonController@get_all');
Route::middleware('admin')->get('lesson/{id}','LessonController@get_single');
Route::middleware('admin')->post('lesson/edit/{id}','LessonController@edit');
Route::middleware('admin')->delete('lesson/{id}','LessonController@delete');
Route::middleware('admin')->post('lesson/add/{id}','LessonController@add');
Route::middleware('admin')->post('lesson/force/add/{id}','LessonController@add_force');
Route::middleware('admin')->post('lesson/force/remove/{id}','LessonController@remove_force');

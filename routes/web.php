<?php


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('datatable/users', 'DataTable\UserController');
Route::resource('datatable/plans', 'DataTable\PlanController');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/users', 'Admin\UserController@index');
Route::get('/admin/plans', 'Admin\PlanController@index');

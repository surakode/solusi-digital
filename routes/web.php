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


Route::get('/', 'AuthControllers@login');
Route::get('login', 'AuthControllers@login')->name('login'); //index login
Route::post('authenticate', 'AuthControllers@authenticate')->name('authenticate'); //post data login
Route::get('auth/forgot', 'AuthControllers@forgot')->name('forgot'); //index forgot login
Route::post('auth/forgotRequest', 'AuthControllers@forgotRequest')->name('forgotRequest'); //post forgot login
Route::get('auth/recover/{hash}', 'AuthControllers@recover')->name('recover'); //index edit auth/password forgot login
Route::post('auth/recoverRequest', 'AuthControllers@recoverRequest')->name('recoverRequest'); //post edit auth/password login

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'HomeController@index')->name('dashboard');
    Route::get('dashboard/create', 'HomeController@create')->name('dashboard.create');
    Route::post('dashboard/store', 'HomeController@store')->name('dashboard.store');
    Route::post('dashboard/addCart', 'HomeController@addCart')->name('dashboard.addCart');
    Route::post('dashboard/deleteCart', 'HomeController@deleteCart')->name('dashboard.deleteCart');
    Route::get('logout', 'AuthControllers@logout')->name('logout');
});


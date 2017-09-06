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

use App\Item;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::name('item.index')->get('items', 'ItemController@index');
Route::name('item.save')->post('/items/save', 'ItemController@save');
Route::name('item.copy')->post('/items/copy/{item}', 'ItemController@copy');
Route::name('item.delete')->delete('/items/{item}/', 'ItemController@delete');
Route::name('item.update')->post('items/{item}/save', 'ItemController@update');


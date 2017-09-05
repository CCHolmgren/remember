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

Route::get('/items', function() {
    return ['items' => Item::all()];
});

Route::post('/items/{item}/save', function($item) {
    $item = Item::find($item);
    $item->fill(request()->only('name', 'type'));
    $item->content = collect(request()->only('content')['content'])->reject(function($item) {
        return array_get($item, 'key') == null || trim(array_get($item, 'key')) == "";
    })->all();
    $item->save();

    return ['item' => Item::find($item->id)];
});

Route::delete('/items/{item}/', function(Item $item) {
    $item->delete();
});

Route::post('/items/save', function() {
    $item = new Item(request()->only('name', 'type'));
    $item->user_id = 1;

    $item->content = collect(request()->only('content')['content'])->reject(function($item) {
        return array_get($item, 'key') == null || trim(array_get($item, 'key')) == "";
    })->all();

    $item->save();

    return ['item' => Item::find($item->id)];
});

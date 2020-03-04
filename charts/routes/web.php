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

//Route::get('stock','Home\HomeController@stock');
//Route::get('detail','Home\HomeController@detail');
Route::get('delete','Home\HomeController@delete');



Route::get('stock','Home\HomeController@stockCate');
Route::get('stockincate','Home\HomeController@stockInCate');
Route::post('newstock','Home\HomeController@newstock');
Route::get('detail','Home\HomeController@detail');
Route::post('add','Home\HomeController@add');//添加股票数据
Route::get('delete','Home\HomeController@delete');
//删除股票
Route::get('deleteStock','Home\HomeController@deleteStock');

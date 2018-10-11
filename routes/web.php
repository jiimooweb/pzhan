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

Route::post('/login','Api\LoginController@login')->middleware(['cors']);

Route::post('/add', 'Api\Users\UserController@store');


Route::group(['middleware' => ['cors', 'token']], function () {

    //图片
    Route::post('pictures/upload', 'Api\Pictures\PictureController@upload');
    Route::post('pictures/delete', 'Api\Pictures\PictureController@delete');
    Route::apiResource('pictures', 'Api\Pictures\PictureController');

    //标签
    Route::apiResource('tags', 'Api\Tags\TagController');

    //轮播图
    Route::apiResource('/swipers', '\App\Api\Controllers\Commons\SwiperController');   
    
    //轮播图组
    Route::get('/swiper_groups/display', '\App\Api\Controllers\Commons\SwiperGroupController@display'); 
    Route::apiResource('/swiper_groups', '\App\Api\Controllers\Commons\SwiperGroupController'); 
    
});
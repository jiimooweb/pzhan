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

    //粉丝专辑（相册）
    Route::post('albums/change', 'Api\Fans\AlbumController@change');
    Route::post('albums/upload', 'Api\Fans\AlbumController@upload');
    Route::apiResource('albums', 'Api\Fans\AlbumController');

    //粉丝相片
    Route::post('photo/change', 'Api\Fans\PhotoController@change');
    Route::post('photo/upload', 'Api\Fans\PhotoController@upload');
    Route::apiResource('photo', 'Api\Fans\PhotoController');

    //粉丝朋友圈
    Route::post('socials/change', 'Api\Fans\SocialController@change');
    Route::post('socials/upload', 'Api\Fans\SocialController@upload');
    Route::apiResource('socials', 'Api\Fans\SocialController');
    
});
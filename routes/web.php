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

    Route::post('qiniu/upload', 'Controller@upload');  //上传图片
    Route::post('qiniu/delete', 'Controller@delete');   //删除图片
    //图片
    Route::post('pictures/{picture}/collect', 'Api\Pictures\PictureController@collect'); //收藏
    Route::post('pictures/{picture}/uncollect', 'Api\Pictures\PictureController@uncollect'); //取消收藏
    Route::post('pictures/{picture}/like', 'Api\Pictures\PictureController@like');  //点赞
    Route::post('pictures/{picture}/unlike', 'Api\Pictures\PictureController@unlike');  //取消赞
    Route::apiResource('pictures', 'Api\Pictures\PictureController');

    //标签
    Route::apiResource('tags', 'Api\Tags\TagController');

    //轮播图
    Route::apiResource('/swipers', 'Api\Swipers\SwiperController');   

    //轮播图组
    Route::get('/swiper_groups/{swiper_group}/change', 'Api\Swipers\SwiperGroupController@change'); 
    Route::get('/swiper_groups/display', 'Api\Swipers\SwiperGroupController@display'); 
    Route::apiResource('/swiper_groups', 'Api\Swipers\SwiperGroupController'); 

    //粉丝专辑（相册）
    Route::post('albums/change', 'Api\Fans\AlbumController@change');
    Route::apiResource('albums', 'Api\Fans\AlbumController');

    //粉丝相片
    Route::post('photo/change', 'Api\Fans\PhotoController@change');
    Route::apiResource('photo', 'Api\Fans\PhotoController');

    //粉丝朋友圈
    Route::post('socials/change', 'Api\Fans\SocialController@change');
    Route::post('socials/upload', 'Api\Fans\SocialController@upload');
    Route::apiResource('socials', 'Api\Fans\SocialController');
    
});
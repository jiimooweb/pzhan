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

Route::group(['prefix' => 'wechat/token/'], function() {
    Route::get('verifyToken', 'Api\Fans\FanController@verifyToken');  //验证Token
    Route::post('getToken', 'Api\Fans\FanController@getToken');  //获取Token
});

Route::get('getBgIdByTime', function() {
    return \App\Utils\Common::getBgIdByTime();
});

Route::get('/get', function() {
    $date = date('Y-m-d H:i:s', time());
    $date = strtotime($date);
    return $date;
    return \Carbon\Carbon::parse($date);
});


Route::group(['middleware' => ['cors', 'token']], function () {
    Route::post('wechat/token/saveInfo', 'Api\Fans\FanController@saveInfo');  //存用户信息    
    Route::get('getUid', 'Api\Fans\FanController@getUid');  //获取用户fan_id
    
    Route::post('qiniu/upload', 'Controller@upload');  //上传图片
    Route::post('qiniu/delete', 'Controller@delete');   //删除图片

    //图片
    /*** 小程序 ***/
    Route::get('pictures/rank', 'Api\Pictures\PictureController@rank');  //排行榜
    Route::get('pictures/app_list', 'Api\Pictures\PictureController@app_list'); 
    Route::get('pictures/{picture}/app_show', 'Api\Pictures\PictureController@app_show'); 
    Route::post('pictures/{picture}/collect', 'Api\Pictures\PictureController@collect'); //收藏
    Route::post('pictures/{picture}/uncollect', 'Api\Pictures\PictureController@uncollect'); //取消收藏
    Route::post('pictures/{picture}/like', 'Api\Pictures\PictureController@like');  //点赞
    Route::post('pictures/{picture}/unlike', 'Api\Pictures\PictureController@unlike');  //取消赞
    /*** 后台 ***/
    Route::apiResource('pictures', 'Api\Pictures\PictureController');

    //标签
    Route::get('tags/all', 'Api\Tags\TagController@all');
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
    Route::get('socials/{social}/comments', 'Api\Fans\SocialController@comments');
    Route::post('socials/{social}/comment', 'Api\Fans\SocialController@comment');
    Route::post('socials/{social}/like', 'Api\Fans\SocialController@like');
    Route::post('socials/uploadPhoto', 'Api\Fans\SocialController@uploadPhoto');
    Route::post('socials/deleteComment', 'Api\Fans\SocialController@deleteComment');
    Route::get('socials/list', 'Api\Fans\SocialController@list');
    Route::post('socials/replys', 'Api\Fans\SocialController@replys');
    Route::post('socials/change', 'Api\Fans\SocialController@change');
    Route::post('socials/upload', 'Api\Fans\SocialController@upload');
    Route::apiResource('socials', 'Api\Fans\SocialController');

    //今日推荐
    Route::post('todays/search','Api\Todays\TodayController@search');
    Route::post('todays/delete','Api\Todays\TodayController@delete');
    Route::apiResource('todays', 'Api\Todays\TodayController');
    Route::apiResource('todayLikes', 'Api\TodayLikes\TodayLikeController');

    // 专题
    Route::apiResource('specials', 'Api\Specials\SpecialController');
    Route::post('specials/switch','Api\Specials\SpecialController@updateSwitch');

    // 新增专题评论
    Route::post('specials/comment','Api\Specials\SpecialCommentController@store');

    // 评论管理
    Route::post('comments/query','Api\Comments\CommentController@queryComments');
    Route::get('comments','Api\Comments\CommentController@index');
    Route::post('comments/delete','Api\Comments\CommentController@delete');

   //黑名单
    
    Route::post('blacklist/ban', 'Api\Blacklists\BlacklistController@banList');
    Route::get('blacklist/seal', 'Api\Blacklists\BlacklistController@sealList');
    Route::apiResource('blacklist', 'Api\Blacklists\BlacklistController');


    //签到
    Route::post('sign_in','Api\Fans\SignInController@signIn');
    Route::apiResource('sign_tasks','Api\Fans\SignInController');
    //粉丝收藏
    Route::get('fans/{fan}/collect', 'Api\Fans\FanController@collect');  //点赞
    //粉丝点赞
    Route::get('fans/{fan}/like', 'Api\Fans\FanController@like');  //点赞

    //举报
    Route::post('report','Api\Blacklists\ReportController@store');
    Route::post('show_report','Api\Blacklists\ReportController@show');
    Route::apiResource('report_causes','Api\Blacklists\ReportCauseController');


    //分享
    Route::post('share','Api\Fans\ShareController@share');
    Route::post('share_show','Api\Fans\ShareController@showShare');
});

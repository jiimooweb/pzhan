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



Route::get('pictures/poster','Api\Pictures\PictureController@createPoster')->name('poster'); //生成海报页面     

Route::group(['middleware' => ['cors', 'token']], function () {
    
    Route::post('wechat/token/saveInfo', 'Api\Fans\FanController@saveInfo');  //存用户信息    
    Route::get('getUid', 'Api\Fans\FanController@getUid');  //获取用户fan_id
    Route::get('getUserInfo', 'Api\Fans\FanController@getUserInfo');  //获取用户信息
    
    Route::post('qiniu/token', 'Controller@getToken');   //获取token    
    Route::post('qiniu/upload', 'Controller@upload');  //上传图片
    Route::post('qiniu/delete', 'Controller@delete');   //删除图片

    //图片
    /*** 小程序 ***/
    Route::get('pictures/{picture}/poster','Api\Pictures\PictureController@poster');//生成海报   
    Route::get('pictures/search-author', 'Api\Pictures\PictureController@searchAuthor');  //搜索作者
    Route::get('pictures/search', 'Api\Pictures\PictureController@search');  //搜索图片
    Route::get('pictures/rank', 'Api\Pictures\PictureController@rank');  //排行榜
    Route::get('pictures/app_list', 'Api\Pictures\PictureController@appList'); 
    Route::get('pictures/getListByTags', 'Api\Pictures\PictureController@getListByTags'); 
    // Route::get('pictures/get-list-by-tags', 'Api\Pictures\PictureController@getListByTags'); 
    Route::get('pictures/get-list-by-author', 'Api\Pictures\PictureController@getListByAuthor'); 
    Route::get('pictures/{picture}/hidden', 'Api\Pictures\PictureController@changeHidden');  //改变隐藏
    Route::get('pictures/{picture}/app_show', 'Api\Pictures\PictureController@appShow'); 
    Route::post('pictures/hidden', 'Api\Pictures\PictureController@hiddenChangeAll');  //改变全部图片隐藏
    Route::post('pictures/{picture}/app_show', 'Api\Pictures\PictureController@appShowByIds'); 
    Route::post('pictures/status', 'Api\Pictures\PictureController@changeStatus'); 
    Route::post('pictures/random', 'Api\Pictures\PictureController@appRandomList'); 
    Route::post('pictures/{picture}/download', 'Api\Pictures\PictureController@download');
    Route::post('pictures/{picture}/collect', 'Api\Pictures\PictureController@collect'); //收藏
    Route::post('pictures/{picture}/uncollect', 'Api\Pictures\PictureController@uncollect'); //取消收藏
    Route::post('pictures/{picture}/like', 'Api\Pictures\PictureController@like');  //点赞
    Route::post('pictures/{picture}/unlike', 'Api\Pictures\PictureController@unlike');  //取消赞
    Route::get('pictures/{picture}/add_hot', 'Api\Pictures\PictureController@addHot');  //增加热度
    
    /*** 后台 ***/
    Route::apiResource('pictures', 'Api\Pictures\PictureController');

    //标签
    Route::get('tags/hot', 'Api\Tags\TagController@getHots');
    Route::get('tags/all', 'Api\Tags\TagController@all');
    Route::get('tags/random', 'Api\Tags\TagController@random');
    Route::post('tags/{tag}/hidden', 'Api\Tags\TagController@changeHidden');
    Route::post('tags/hidden', 'Api\Tags\TagController@changeHiddenAll');
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
    Route::post('socials/{social}/addCommentNotice', 'Api\Fans\SocialController@addCommentNotice');
    Route::post('socials/{social}/addReplyNotice', 'Api\Fans\SocialController@addReplyNotice');
    Route::post('socials/replys', 'Api\Fans\SocialController@replys');
    Route::post('socials/change', 'Api\Fans\SocialController@change');
    Route::post('socials/upload', 'Api\Fans\SocialController@upload');
    Route::apiResource('socials', 'Api\Fans\SocialController');

    //通知
    Route::get('notices/comment', 'Api\Fans\NoticeController@comment');
    Route::get('notices/like', 'Api\Fans\NoticeController@like');
    Route::apiResource('notices', 'Api\Fans\NoticeController');


    // 评论管理
    Route::post('comments/query','Api\Comments\CommentController@queryComments');
    Route::get('comments','Api\Comments\CommentController@index');
    Route::post('comments/delete','Api\Comments\CommentController@delete');

   //黑名单
    
    Route::post('blacklist/ban', 'Api\Blacklists\BlacklistController@banList');
    Route::get('blacklist/isban', 'Api\Blacklists\BlacklistController@isban');
    Route::get('blacklist/seal', 'Api\Blacklists\BlacklistController@sealList');
    Route::apiResource('blacklist', 'Api\Blacklists\BlacklistController');

    //签到
    Route::get('get_sign','Api\Fans\SignInController@get_sign');
    Route::post('sign_in','Api\Fans\SignInController@signIn');
    Route::apiResource('sign_tasks','Api\Fans\SignInController');
    //粉丝收藏
    Route::get('fans/{fan}/collect', 'Api\Fans\FanController@collect');  //收藏
    Route::get('fans/{fan}/download', 'Api\Fans\FanController@download');  //收藏
    //粉丝点赞
    Route::get('fan/point-and-share-count', 'Api\Fans\FanController@getPointAndShareCount');      
    Route::get('fans/{fan}/like', 'Api\Fans\FanController@like');  //点赞
    Route::get('fans/fan_pictures', 'Api\Fans\FanController@fanPicture');  //点赞
    Route::get('fans', 'Api\Fans\FanController@fans');  //获取用户

    //举报
    Route::post('verify','Api\Blacklists\ReportController@verify');
    Route::post('report','Api\Blacklists\ReportController@store');
    Route::post('show_report','Api\Blacklists\ReportController@show');
    Route::apiResource('report_causes','Api\Blacklists\ReportCauseController');

    //分享
    Route::post('share','Api\Fans\ShareController@share');
    Route::post('share_show','Api\Fans\ShareController@showShare');

    //今日推荐
    Route::get('todays/mini','Api\Todays\TodayController@getToday');
    Route::post('todays/date','Api\Todays\TodayController@getDate');
    Route::post('todays/other','Api\Todays\TodayController@getOther');
    Route::post('todays/month','Api\Todays\TodayController@getDataByYearMonth');
    Route::post('todays/year','Api\Todays\TodayController@getDataByYear');
    Route::post('todays/search','Api\Todays\TodayController@search');
    Route::post('todays/delete','Api\Todays\TodayController@delete');
    Route::get('todays/one','Api\Todays\TodayController@getOne');
    Route::apiResource('todays', 'Api\Todays\TodayController');


    //今日点赞
    Route::post('todayLikes/delete','Api\TodayLikes\TodayLikeController@delete');
    Route::apiResource('todayLikes', 'Api\TodayLikes\TodayLikeController');

    // 专题
    Route::get('specials/hot','Api\Specials\SpecialController@getHot');
    Route::get('specials/mini','Api\Specials\SpecialController@miniIndex');
    Route::post('specials/res','Api\Specials\SpecialController@getRes');
    Route::post('specials/search','Api\Specials\SpecialController@doSearch');
    Route::post('specials/switch','Api\Specials\SpecialController@updateSwitch');
    Route::apiResource('specials', 'Api\Specials\SpecialController');

    //专题评论
    Route::get('specials/{special}/comments', 'Api\Specials\SpecialCommentController@getcomments');
    Route::post('specials/{special}/comment', 'Api\Specials\SpecialCommentController@comment');
    Route::post('specials/{special}/addCommentNotice', 'Api\Specials\SpecialCommentController@addCommentNotice');
    Route::post('specials/{special}/addReplyNotice', 'Api\Specials\SpecialCommentController@addReplyNotice');
    Route::post('specials/replys', 'Api\Specials\SpecialCommentController@replys');
    Route::post('specials/deleteComment', 'Api\Specials\SpecialCommentController@deleteComment');

    //文章
    Route::apiResource('articles', 'Api\Articles\ArticleController');
    //广告
    Route::get('ads/app', 'Api\Ads\AdController@app');
    Route::apiResource('ads', 'Api\Ads\AdController');
    //公告
    Route::get('announcements/app', 'Api\Announcements\AnnouncementController@app');
    Route::apiResource('announcements', 'Api\Announcements\AnnouncementController');

    // 每日排行
    Route::post('leaderDates/date', 'Api\Leaderboards\LeaderDateController@getDate');
    Route::get('leaderDates/date', 'Api\Leaderboards\LeaderDateController@getDateforSP');
    Route::post('leaderDates/data', 'Api\Leaderboards\LeaderDateController@getDataByDate');
    Route::apiResource('leaderDates','Api\Leaderboards\LeaderDateController');
    Route::apiResource('leaderboards','Api\Leaderboards\LeaderboardController');

});



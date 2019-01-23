<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Models\Photo;
use App\Utils\Common;
use App\Utils\Module;
use App\Models\Notice;
use App\Models\Social;
use App\Services\Qiniu;
use App\Services\Token;
use App\Models\LikeNotice;
use App\Models\SocialLike;
use App\Models\PointHistory;
use App\Models\SocialReward;
use Illuminate\Http\Request;
use App\Models\CommentNotice;
use App\Models\SocialComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialRequest;

class SocialController extends Controller
{
    public function webIndex() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $socials = Social::with(['photos','fan'])->withCount(['likeFans', 'comments', 'photos'])->orderBy('created_at', 'desc')->paginate(10);
        foreach($socials as &$social) {
            $social->like = $social->isLike($fan_id) ? 1 : 0;
            if($social->photos_count == 1) {
                $social->img_type = Common::getImageType($social->photos[0]['url']);
            }else {
                $social->img_type = 0;
            }
        }
        return response()->json(['status' => 'success', 'data' => $socials]);   
    }

    public function index() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $socials = Social::with(['photos','fan'])->where('hidden', 0)->withCount(['likeFans', 'comments', 'photos'])->orderBy('created_at', 'desc')->paginate(10);
        foreach($socials as &$social) {
            $social->like = $social->isLike($fan_id) ? 1 : 0;
            if($social->photos_count == 1) {
                $social->img_type = Common::getImageType($social->photos[0]['url']);
            }else {
                $social->img_type = 0;
            }
        }
        return response()->json(['status' => 'success', 'data' => $socials]);   
    }

    public function list() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $socials = Social::where('fan_id', $fan_id)->where('hidden', 0)->with(['photos','fan'])->withCount(['likeFans', 'comments', 'photos'])->orderBy('created_at', 'desc')->paginate(10);
        foreach($socials as &$social) {
            $social->like = $social->isLike($fan_id) ? 1 : 0;
            if($social->photos_count == 1) {
                $social->img_type = Common::getImageType($social->photos[0]['url']);
            }else {
                $social->img_type = 0;
            }
        }
        return response()->json(['status' => 'success', 'data' => $socials]); 
    }

    public function store(SocialRequest $request) 
    {   
        $data = request()->all();  
        $data['fan_id'] = request('fan_id') ?? Token::getUid(); 
        $social = Social::create($data);
        if($social) {
            $point = 0;
            $date = date('Y-m-d', time());
            $socialReward = SocialReward::where('fan_id', $data['fan_id'])->whereDate('created_at', $date)->count();
            $socialFirst = SocialReward::where('fan_id',$data['fan_id'])->count();
            if($socialFirst == 0 ){
                $point = 50;
                SocialReward::firstOrCreate(['fan_id' => $data['fan_id'], 'social_id' => $social->id, 'point' => $point]);
                Fan::where('id', $data['fan_id'])->increment('point', $point);
                PointHistory::firstOrCreate([
                    'fan_id' => $data['fan_id'],
                    'state' => 1,
                    'point' => $point,
                    'tag' => 'social',
                    'comment' => '每日首次发布动态获得:' .$point. '积分'
                ]);
            }

            if($socialFirst != 0 && $socialReward == 0) {
                $fan = Fan::find($data['fan_id']);
                if($fan->point < 100 ){
                    $point = rand(20,50);
                }else{
                    $point = rand(9,50);
                }
                SocialReward::firstOrCreate(['fan_id' => $data['fan_id'], 'social_id' => $social->id, 'point' => $point]);
                Fan::where('id', $data['fan_id'])->increment('point', $point);
                PointHistory::firstOrCreate([
                    'fan_id' => $data['fan_id'],
                    'state' => 1,
                    'point' => $point,
                    'tag' => 'social',
                    'comment' => '每日首次发布动态获得:' .$point. '积分'
                ]);
            }
            return response()->json(['status' => 'success', 'data' => $social->where('id', $social->id)->with(['photos','fan'])->withCount(['likeFans', 'comments', 'photos'])->first(), 'point' => $point]);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                           
    }

    public function show()
    {
        $fan_id = request('fan_id') ?? Token::getUid();        
        $social = Social::where('id', request()->social)->with(['photos','fan'])->withCount(['photos','likeFans', 'comments'])->first();
        $social->like = $social->isLike($fan_id) ? 1 : 0;        
        if($social->photos_count == 1) {
            $social->img_type = Common::getImageType($social->photos[0]['url']);
        }else {
            $social->img_type = 0;
        }        
        $status = $social ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $social]);   
    }

    public function update(SocialRequest $request)
    {
        $data = request()->all();                      
        if(Social::where('id', request()->social)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                            
    }

    public function destroy()
    {
        if(Social::where('id', request()->social)->delete()) {
            SocialComment::where('social_id', request()->social)->delete();
            return response()->json(['status' => 'success', 'msg' => '删除成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);     
    }

    public function change() 
    {
        if(Social::where('id', request('id'))->update(['hidden' => request('hidden')])) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']); 
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);     
    }

    public function comments()
    {

        $comments = SocialComment::where([ 'social_id' => request()->social, 'pid' => 0 ])->with(['fan', 'toFan'])->withCount('replys')->orderBy('created_at', 'asc')->get();

        foreach($comments as $key => &$comment) {
            if($key < 3) {
                 $comment->replys = SocialComment::where('pid',$comment->id)->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->limit(2)->get();
            }else {
                $comment->replys = [];
            }
        }
        return response()->json(['status' => 'success', 'data' => $comments]);
    }

    public function comment(Social $social)
    {
        $comment = request()->all();

        $comment = SocialComment::firstOrCreate($comment);
        
        if($comment) {
            
            return response()->json(['status' => 'success', 'data' => $comment->where('id', $comment->id)->with(['replys','fan', 'toFan'])->withCount(['replys'])->first()]);
        }

        return response()->json(['status' => 'error']);
        
    }

    public function addCommentNotice(Social $social) 
    {
        $notice_fans = [];
        $data = request()->all();
        $fan_id = request('fan_id') ?? Token::getUid(); 

        //(他人)评论动态
        if($fan_id != $social->fan_id) {
            array_push($notice_fans, $social->fan_id);
        }

        if($data['to_fan_id'] > 0 && $data['to_fan_id'] != $fan_id) {
            array_push($notice_fans, $data['to_fan_id']);
        }

        $notice_fans = array_unique($notice_fans);   
        
        if($notice_fans) {
            
            foreach($notice_fans as $notice_fan) {
                
                $notice = [
                    'fan_id' => $notice_fan,
                    'from_fan_id' => $fan_id,
                    'to_fan_id' => $data['to_fan_id'],
                    'content' => $data['content'],
                    'module_id' => $social->id,
                    'module_comment_id' => $data['module_comment_id'],                    
                    'module' => Module::Social,
                ];  

                CommentNotice::firstOrCreate($notice);
            }
        }
       
        return response()->json(['status' => 'success']);

        
    }

    public function addReplyNotice(Social $social) {
        $notice_fans = [];
        $data = request()->all();
        $fan_id = request('fan_id') ?? Token::getUid(); 
        $comment_fan_id = request('comment_fan_id');
        
        if($fan_id != $social->fan_id) {
            array_push($notice_fans, $social->fan_id); 
        }

        if($fan_id != $comment_fan_id) {
            array_push($notice_fans, $comment_fan_id);
        }

        if($data['to_fan_id'] > 0 && $data['to_fan_id'] != $fan_id) {
            array_push($notice_fans, $data['to_fan_id']);
        }

        $notice_fans = array_unique($notice_fans);

        if($notice_fans) {
            foreach($notice_fans as $notice_fan) {
                $notice = [
                    'fan_id' => $notice_fan,
                    'from_fan_id' => $fan_id,
                    'to_fan_id' => $data['to_fan_id'],
                    'content' => $data['content'],
                    'module_id' => $social->id,
                    'module_comment_id' => $data['module_comment_id'],
                    'module' => Module::Social,
                ];  
                CommentNotice::firstOrCreate($notice);
            }
        }
       
        return response()->json(['status' => 'success']);
    }

 
    public function replys() 
    {
        
        $comment = SocialComment::where('id', request()->id)->with(['fan', 'toFan'])->withCount('replys')->orderBy('created_at', 'asc')->first();
        $replys = SocialComment::where('pid', request()->id)->with(['fan', 'toFan'])->orderBy('created_at', 'asc')->get();

        return response()->json(['status' => 'success', 'comment' => $comment,'replys' => $replys]);
        
    }

    public function deleteComment() 
    {
        $delete_count = 0;
        if(SocialComment::where('id' ,request()->id)->delete()) {
            CommentNotice::where('module_comment_id', request()->id)->delete();
            $delete_count = SocialComment::where('pid', request()->id)->forceDelete();
            return response()->json(['status' => 'success', 'count' => $delete_count + 1]);
        }

        return response()->json(['status' => 'error']);        
    }

    public function like(Social $social)
    {
        $data =request()->all();

        $fan_id = request('fan_id') ?? Token::getUid(); 
        
        $like = SocialLike::firstOrCreate(['fan_id' => $fan_id, 'social_id' => $social->id]);
        
        if($like ) {
            // $likeCount = SocialLike::where('social_id' , $social->id)->count();
            // if($likeCount % 5 == 0) {
            //     $fan = Social::where('id', $social->id)->first()['fan_id'];
            //     Fan::where('id', $fan)->increment('point', 50);
            //     PointHistory::firstOrCreate([
            //         'fan_id' =>  $fan_id,
            //         'state' => 1,
            //         'point' => 50,
            //         'tag' => 'social',
            //         'comment' => '点赞活动'.$likeCount.'个赞获得的50积分'
            //     ]);
            // }
            if($fan_id != $social->fan_id) {
                //添加通知
                $notice = [
                    'fan_id' => $social->fan_id,
                    'from_fan_id' => $fan_id,
                    'module_id' => $social->id,
                    'module' => Module::Social,
                ];

                LikeNotice::firstOrCreate($notice);
            }
            
            return response()->json(['status' => 'success', 'data' => $like]);
        }

        return response()->json(['status' => 'error']);
    }

    public function uploadPhoto() {
        $social_id = request('social_id');
        $fan_id = request('fan_id') ?? Token::getUid();    
        $file = request('file');
        $url = \App\Services\Qiniu::upload($file);
        if($url) {
            if(Photo::create(['fan_id' => $fan_id, 'social_id' => $social_id, 'url' => $url])) {
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);

    }

    public function changeHidden(Social $social)
    {
        if($social->hidden == 0) {
            $social->hidden = 1;
        } else {
            $social->hidden = 0;
        }

        if($social->save()) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error']);  
    }

    public function hiddenChangeAll()
    {
        $hidden = request('hidden');

        Social::where('id', '>', 0)->update(['hidden' => $hidden]);

        return response()->json(['status' => 'success']);     
    }



}

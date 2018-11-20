<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Picture;
use App\Services\Token;

class PictureTag extends Model
{
    protected $table = 'picture_tags';

    public $timestamps = false;

    public static function getRecommends(int $picture_id, int $limit = 30) 
    {
        $fan_id = Token::getUid();
        $tags = self::where('picture_id',$picture_id)->get()->pluck('tag_id');
        $picture_ids = self::whereIn('tag_id', $tags)->whereNotIn('picture_id', [$picture_id])->inRandomOrder()->limit($limit)->get()->pluck('picture_id');
        
        $recommends = Picture::whereIn('id', $picture_ids)->where('hidden', 0)->get();
        foreach($recommends as &$recommend) {
            $recommend->collect = $recommend->isCollect($fan_id) ? 1 : 0;
            $recommend->like = $recommend->isLike($fan_id) ? 1 : 0;
        }
        
        return $recommends;
    }

    public static function getRecommendsByIds(array $recommend_ids) 
    {
        $fan_id = Token::getUid();
        $recommends = Picture::whereIn('id', $recommend_ids)->where('hidden', 0)->get();
        foreach($recommends as &$recommend) {
            $recommend->collect = $recommend->isCollect($fan_id) ? 1 : 0;
            $recommend->like = $recommend->isLike($fan_id) ? 1 : 0;
        }
        
        return $recommends;
    }
}

<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Picture;

class PictureTag extends Model
{
    protected $table = 'picture_tags';

    public $timestamps = false;

    public static function getRecommends(int $picture_id, int $limit = 30) 
    {
        $tags = self::where('picture_id',$picture_id)->get()->pluck('tag_id');
        $picture_ids = self::whereIn('tag_id', $tags)->whereNotIn('picture_id', [$picture_id])->inRandomOrder()->limit($limit)->get()->pluck('picture_id');
        $recommends = Picture::whereIn('id', $picture_ids)->with(['tags'])->get();
        return $recommends;
    }
}

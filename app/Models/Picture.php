<?php

namespace App\Models;

use App\Models\Model;
use App\Models\DownloadPicture;

class Picture extends Model
{

    public static function boot()
    {
        parent::boot();

    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'picture_tags', 'picture_id', 'tag_id');
    }

    public function isCollect(int $fan_id) 
    {
       return CollectPicture::where(['fan_id' => $fan_id, 'picture_id' => $this->id ])->exists();
    }

    public function isLike(int $fan_id) 
    {
        return LikePicture::where(['fan_id' => $fan_id, 'picture_id' => $this->id ])->exists();
    }

    public function isDownload(int $fan_id) 
    {
        return DownloadPicture::where(['fan_id' => $fan_id, 'picture_id' => $this->id ])->exists();
    }

    public function collect(int $fan_id) 
    {
        return $this->hasOne(CollectPicture::class)->where(['fan_id' => $fan_id, 'picture_id' => $this->id ]);
    }

    public function like(int $fan_id) 
    {
        return $this->hasOne(LikePicture::class)->where(['fan_id' => $fan_id, 'picture_id' => $this->id ]);
    }

    public function collectFans() 
    {
        return $this->belongsToMany(Fan::class, 'fan_collect_pictures', 'picture_id', 'fan_id');
    }

    public function likeFans() 
    {
        return $this->belongsToMany(Fan::class, 'fan_like_pictures', 'picture_id', 'fan_id');
    }

    public function downloadFans() 
    {
        return $this->belongsToMany(Fan::class, 'fan_download_pictures', 'picture_id', 'fan_id');
    }

    public static function refershRank()
    {   
        //收藏排行
        $collectRank = self::where('hidden', 0)->withCount(['collectFans'])->orderBy('collect_fans_count', 'desc')->limit(100)->get()->toArray();
        \Cache::store('redis')->put('collectRank', $collectRank, 1440);

        //点赞排行
        // $likeRank = self::where('hidden', 0)->withCount(['likeFans'])->orderBy('like_fans_count', 'desc')->limit(100)->get()->toArray();
        // \Cache::store('redis')->put('likeRank', $likeRank, 1440);

        //下载排行
        $downloadRank = self::where('hidden', 0)->withCount(['downloadFans'])->orderBy('download_fans_count', 'desc')->limit(100)->get()->toArray();
        \Cache::store('redis')->put('downloadRank', $downloadRank, 1440);

        //热度排行
        $hotRank = self::where('hidden', 0)->orderBy('hot', 'desc')->limit(100)->get()->toArray();
        \Cache::store('redis')->put('hotRank', $hotRank, 1440);
    }

}

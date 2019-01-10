<?php

namespace App\Utils;

class Common 
{
    public static function getCommentTree($arr,$pid = 0,$step = 0){
        global $tree,$comment;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                $tree[] = $val;
            }else {
                $tree[$val['pid']]['comment'][] = $val;                    
                $tree[$val['pid']]['pid'] = $val['pid'];                    
            }   
        }

        foreach($tree as $key => $val) {
            if($val['pid'] == 0) {
                if(isset($tree[$val['id']]) && isset($tree[$val['id']]['comment'])) {
                    $tree[$key]['comment'] = $tree[$val['id']]['comment'];
                }   
                $comment[$key] = $tree[$key];
            }
        }

        return array_values($comment);
        
    }

    public static function getBgIdByTime() {
        $id = 0;
        $hour = date('H', time());
        switch($hour) {
            case $hour < 6:
                $id = 0;
                break;
            case $hour < 8:
                $id = 1;
                break;
            case $hour < 10:
                $id = 2;
                break;
            case $hour < 12:
                $id = 3;
                break;  
            case $hour < 14:
                $id = 4;
                break; 
            case $hour < 16:
                $id = 5;
                break;  
            case $hour < 18:
                $id = 6;
                break; 
            case $hour < 20:
                $id = 7;
                break; 
            case $hour < 24:
                $id = 8;
                break; 
            default:
                break;
        }

        return $id;
    }


    /**
     * @param $arr
     * @param $key_name
     * @return array
     * 将数据库中查出的列表以指定的 id 作为数组的键名 
     */
    public static function convert_arr_key($arr, $key_name)
    {
        $result = array();
        foreach($arr as $key => $val){
            $result[$val[$key_name]] = $val;
        }
        return $result;
    }

    public static function getImageScale($file = '') {
        $img_type = 0;
        $image = getimagesize($file);
        $width = $image[0];
        $height = $image[1];
        $scale = $height / $width;
        return number_format($scale,2);
    }

    public static function getImageType($file = '') {
        $img_type = 0;
        $image = getimagesize($file);
        $width = $image[0];
        $height = $image[1];
        $scale = $width / $height;
        if ($scale > 1) {
            $img_type = 1;
        } else if ($scale < 1) {
            $img_type = 2;
        } else {
            $img_type = 3;
        }
        return $img_type;
    }

}
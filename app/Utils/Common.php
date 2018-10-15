<?php

namespace App\Utils;

class Common 
{
    public static function getCommentTree($arr,$pid = 0,$step = 0){
        global $tree,$comment;
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                if($val['pid'] == 0) {
                    $tree[] = $val;
                }else {
                    $tree[$val['pid']]['comment'][] = $val;                    
                    $tree[$val['pid']]['pid'] = $val['pid'];                    
                }
                self::getCommentTree($arr , $val['id'] ,$step+1);
            }
        }

        foreach($tree as $key => $val) {
            if($val['pid'] == 0) {
                if(isset($tree[$val['id']])) {
                    $tree[$key]['comment'] = $tree[$val['id']]['comment'];
                }   
                $comment[$key] = $tree[$key];
            }
        }

        return $comment;
        
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

    public static function getLimitRand($start, $end, $limit) 
    {
        $rand = range($start, $end);
        shuffle($rand);
        $rand = array_slice($rand, 0, $limit);
        return $rand;
    }

}
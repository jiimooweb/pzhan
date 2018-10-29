<?php

namespace App\Http\Controllers\Api\Fans;

use App\Utils\Module;
use App\Models\Notice;
use App\Models\Social;
use App\Services\Token;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeController extends Controller
{
    public function index() 
    {
        $fan_id = request('fan_id') ?? Token::getUid();
        $notices = Notice::where('fan_id',$fan_id)->with(['fan', 'fromFan'])->paginate(20); 
        foreach($notices as &$notice)
        if($notice->module == Module::Social) {
            $notice->module = Social::where('id', $notice->module_id)->first(); 
        }

        return response()->json(['status' => 'success', 'data' => $notices]);
        // $socials = Social::with(['photos','fan'])->withCount(['likeFans', 'comments', 'photos'])->orderBy('created_at', 'desc')->paginate(20);       
    }
}

<?php

namespace App\Http\Controllers\Api\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index(Request $request) 
    {
        $articles = Article::when($request->keyword, function($query) use ($request) {
            return $query->where('title', 'like', '%'.$request->keyword.'%');
        })->orderBy('created_at','desc')->paginate(config('common.pagesize'));
        
        return response()->json(['status' => 'success', 'data' => $articles]);
    }


    public function store() 
    {   
        $data = request([
            'title', 'author', 'click', 'content'
        ]);
        
        if(Article::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);                            
        }
            
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);                              
    }

    public function show()
    {
        $article = Article::find(request()->article);
        $article->increment('click', 1);
        $status = $article ? 'success' : 'error';
        return response()->json(['status' => $status, 'data' => $article]);
    }

    public function update()
    {
        $data = request([
            'title', 'author', 'click', 'content'
        ]);
        
        if(Article::where('id', request()->article)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);                
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);                  
    }

    public function destroy()
    {
        // TODO:判断删除权限
        if(Article::where('id', request()->article)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);               
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);                        
    }
}

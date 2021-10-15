<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function getList ()
    {
        $news = News::query()
            ->whereDate('published_at', '<=', date('Y-m-d H:i:s'))
            ->where('is_published',true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(5);
        return view('news_list',['news'=>$news]);
    }
    public function getDetails (string $slug)
    {
        $news_item = News::query()
            ->where('slug',$slug)
            ->whereDate('published_at', '<=', date('Y-m-d H:i:s'))
            ->where('is_published',true)
            ->first();
        if($news_item===null) abort(404);
        return view('news_item',['news_item'=>$news_item]);
    }
}

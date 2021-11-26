<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }
        body {
            margin: 0
        }
        a {
            background-color: transparent
        }
        [hidden] {
            display: none
        }
        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }
        .w-5 {
            width: 1.25rem
        }
    </style>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
    @includeWhen(session()->has('suggest'),'Suggestion')
</head>
    <body >
    <h1>Новости</h1>>
    @foreach ($news as $news_item)
        <a href="{{route('news_item',['slug'=>$news_item->slug])}}">{{ $news_item->title }}</a>
        <p>{{$news_item->published_at}}</p>
        @if($news_item->description != null)
            <p>{{$news_item->description}}</p>
        @endif
    @endforeach

    {{ $news->links() }}
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AppealView</title>

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
        .bordered {
            border: 1px solid black;
        }
        .error {
            color: red;}
    </style>
</head>
    <body >
    <h2>Отправить обращение</h2>
    @if($success)
        <p>Sent successfully</p>
    @endif
    @if($errors !== null)
        @foreach($errors as $error)
            <p class="error">{{$error}}</p>
        @endforeach
    @endif
    <form method = "POST" action="{{route('appeal')}}">
        @csrf
        <div>
            <label>Name</label>
            <input class="bordered" name="name" type="text" value="{{request()->isMethod('post') ? old('name') : ''}}" maxlength="20" size="20"/>
        </div>
        <div>
            <label>Phone</label>
            <input class="bordered" name="phone" type="tel" value="{{request()->isMethod('post') ? old('phone') : ''}}" maxlength="11" size="20"/>
        </div>
        <div>
            <label>Email</label>
            <input class="bordered" name="email" type="email" value="{{request()->isMethod('post') ? old('email') : ''}}" maxlength="100" size="20"/>
        </div>
        <div>
            <label>Message</label>
            <textarea class="bordered" name="message"  maxlength="100" rows="5">{{request()->isMethod('post') ? old('message') : ''}}</textarea>
        </div>
        <input type="submit">
    </form>
    </body>
</html>

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
    @include('auth')
    <h2>Отправить обращение</h2>
    @if ($Message === true)
        <p>Thanks for your feedback</p>
        <a href="{{url()->previous()}}">Way to the back</a>
    @endif
    @if($success)
        <p>Sent successfully</p>
    @endif
    @if($errors !== null)
        @foreach($errors->all() as $error)
            <p class="error">{{$error}}</p>
        @endforeach
    @endif
    <form method = "POST" action="{{route('appeal_post')}}">
        @csrf
        <div>
            <label>Name</label>
            <input class="bordered" name="name" type="text" value="{{$errors !== null  ? old('name') : ''}}" />
        </div>
        <div>
            <label>Surname</label>
            <input class="bordered" name="surname" type="text" value="{{$errors !== null ? old('surname') : ''}}" size="20"/>
        </div>
        <div>
            <label>Patronymic</label>
            <input class="bordered" name="patronymic" type="text" value="{{$errors !== null ? old('patronymic') : ''}}" size="20"/>
        </div>
        <div>
            <label>Age</label>
            <input class="bordered" name="age" type="text"  value="{{$errors !== null ? old('age') : ''}}"/>
        </div>
        <div>
            <label>Phone</label>
            <input class="bordered" name="phone"  type="text" value="{{$errors !== null ? old('phone') : ''}}" size="20"/>
        </div>
        <div>
            <label>Email</label>
            <input class="bordered" name="email" type="text" value="{{$errors !== null ? old('email') : ''}}" size="20"/>
        </div>
        <div>
            <label>Message</label>
            <textarea class="bordered" name="message" rows="5">{{$errors !== null ? old('message') : ''}}</textarea>
        </div>
        <div>
            <label>Gender</label>
            <label>
                <select class="bordered" name="gender">
                    <option value="0" {{$errors !== null && old('gender') == 0 ? 'selected' : ''}}>Male</option>
                    <option value="1" {{$errors !== null && old('gender') == 1 ? 'selected' : ''}}>Female</option>
                </select>
            </label>
        </div>
        <input type="submit">
    </form>
    </body>
</html>

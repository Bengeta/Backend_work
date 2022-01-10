@inject('auth', '\Illuminate\Support\Facades\Auth')
@if ($auth::check())
    <p>
        <a href="{{ route('profile') }}">Профиль</a>
        <a href="{{ route('logout') }}">Выход</a>
    </p>
@else
    <p>
        <a href="{{ route('login') }}">Вход</a>
        <a href="{{ route('registration') }}">Регистрация</a>
    </p>
@endif

<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WebAuthController extends Controller
{
    public function registration(Request $request)
    {
        if (Auth::check())
            return redirect()->route('profile');

        if ($request->isMethod('post')) {
            $request['login'] = strtolower($request['login']);
            $validated = $request->validate([
                'login' => 'unique:users|required|between:5, 30',
                'password' => 'required|between:10, 30|regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&].{10,}$/'
            ]);
            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->login = $validated['login'];
            $user->password = Hash::make($validated['password']);
            $user->save();
            Auth::login($user);
            return redirect()->route('profile');
        }
        return view('registration');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile', ['user' => new UserResource($user)]);
    }

    public function login(Request $request)

    {
        if (Auth::check()) {
            return redirect()->route('profile');
        }
        if ($request->isMethod('post')) {
            $request['login'] = strtolower($request['login']);
            $validated = $request->validate([
                'login' => 'required|between:5, 30',
                'password' => 'required|between:10, 30',
            ]);
            if (Auth::attempt($validated)) {
                return redirect()->route('profile');
            }
            return redirect()->route('login');
        }
        return view('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}

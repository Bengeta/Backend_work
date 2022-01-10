<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function registration(Request $request): JsonResponse
    {
        $request['login'] = strtolower($request['login']);
        $validator = Validator::make($request->all(), [
            'login' => 'unique:users|required|between:5, 30|regex: /^[a-z0-9\-._]+$/i',
            'email' => 'required|email',
            'name' => 'required',
            'password' => 'required|between:10,30|regex:/^(?=(.*[A-Z]){1})(?=(.*[a-z]){1})(?=(.*[0-9]){1})(?=(.*[re@#$%^!&+=.\-_*]){1})([a-zA-Z0-9@#$%^!&+=*.\-_])*$/'
        ]);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['message' => $messages], 422);
        }

        $validated = $validator->validated();
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->login = $validated['login'];
        $user->password = Hash::make($validated['password']);
        $user->save();

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'token' => $token,
            'user' => new UserResource($user),
        ];
        return response()->json($response, 201);
    }

    public function profile(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([new UserResource($user)]);
    }

    public function login(Request $request): JsonResponse
    {
        $request['login'] = strtolower($request['login']);
        $validator = Validator::make($request->all(), [
            'login' => 'required|between:5, 30',
            'password' => 'required|between:10, 30'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            return response()->json(['message' => $messages], 422);
        }

        $validated = $validator->validated();

        if (!Auth::attempt(['login' => $validated['login'], 'password' => $validated['password']])) {
            return response()->json(['message' => 'Wrong login or password. Please, try again.'], 422);
        }

        $user = User::query()->where('login', $validated['login'])->first();
        $token = $user->createToken('token')->plainTextToken;
        $response = [
            'token' => $token,
            'user' => new UserResource($user),
        ];
        return response()->json($response);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Http\Requests\AppealPostRequest;
use App\Http\Sanitizers\PhoneSanitizer;
use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function appealGet(Request $request)
    {
        $success = $request->session()->get('success', false);
        $Message = false;
        if ($request->get('accepted')) {
            if ($request->session()->get('message'))
                $Message = true;
            $request->session()->put('message', false);
        }
        return view('appealView', ['success' => $success, 'Message' => $Message]);

    }

    public function appealPost(AppealPostRequest $request)
    {
        $appeal = new Appeal();
        $appeal->name = $request->input('name');
        $appeal->surname = $request->input('surname');
        $appeal->patronymic = $request->input('patronymic');
        $appeal->age = $request->input('age');
        $appeal->gender = $request->input('gender');
        $appeal->message = $request->input('message');
        $appeal->email = $request->input('email');
        $appeal->phone = PhoneSanitizer::sanitize($request->input('phone'));
        $appeal->save();
        $request->session()->put('appeal', true);
        return redirect()
            ->route('appeal')
            ->with('success', true);

    }
}

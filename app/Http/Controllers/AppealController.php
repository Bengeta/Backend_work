<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $errors = [];
        $success = $request->session()->get('success',false);
        if ($request->isMethod('post')) {
            if ($request['name'] === null) {
                $errors['name'] = 'Name is empty';
            }
            if ($request['message'] === null) {
                $errors['message'] = 'Message is empty';
            }
            if ($request['email'] === null && $request['phone'] === null) {
                $errors['email_phone'] = 'You did not give any contact info';
            }
            if(count($errors)>0)
            {
                $request->flash();
            }
            else{
                $appeal = new Appeal();
                $appeal->name = $request->input('name');
                $appeal->message = $request->input('message');
                $appeal->email = $request->input('email');
                $appeal->phone = $request->input('phone');
                $appeal->save();
                return redirect()
                    ->route('appeal')
                    ->with('success', true);
            }
        }
        return view('appealView', ['errors' => $errors, 'success' => $success]);
    }
}

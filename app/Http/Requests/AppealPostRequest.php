<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppealPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required','string','max:20'],
            'surname' => ['required','string','max:40'],
            'patronymic' => ['nullable','string','max:20'],
            'age' => ['required','integer','between:14,122'],
            'phone' => ['required_without: email','string','max:11','regex:/(^(7|8)(\d){10})|(^([+])(7)(\d){10})/'],
            'email' => ['required_without: phone','string','max:100','regex:/^[^@]+@[^@.]+\.[^@.]+$/'],
            'message' => ['required','string','max:100'],
            'gender' => ['required',Rule::in(Gender::MALE,Gender::FEMALE)]
        ];
    }

}

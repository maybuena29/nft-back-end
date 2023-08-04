<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'contact' => ['required'],
            'address' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
            'city' => ['required'],
            'department' => ['required'],
            'company' => ['required'],
            'role_id' => ['required'],
            'status' => ['required']

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            "message" => $errors->first(),
            "status" => "Validation Failed",
        ], 422);

        throw new HttpResponseException($response);
    }
}

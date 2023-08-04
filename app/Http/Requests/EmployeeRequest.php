<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_id' => ['required', 'unique:tbl_employee,account_id'],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'contact' => ['required'],
            'address' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
            'city' => ['required'],
            'zip_code' => ['required'],
            'department' => ['required'],
            'company' => ['required'],
            'role_id' => ['required'],
            'status' => ['required']
        ];
    }
}

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
            // 'unique:tbl_employee,employee_number,' . $this->employee->id
            'employee_number' => ['required', 'min:5', 'max:20', 'unique:tbl_employee,employee_number'],
            'name' => ['required'],
            'position_id' => ['required'],
            'status' => ['required']
        ];
    }
}

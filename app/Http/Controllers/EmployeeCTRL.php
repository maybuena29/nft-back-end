<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\EmployeeMODEL;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeCTRL extends Controller
{
    //
    public function index(){
        $employees = EmployeeMODEL::orderBy('id')->with('Position')->get();
        // return response()->json("Employee Informations");
        return new EmployeeResource($employees);
    }

    public function showEmployee($id){
        $employee = EmployeeMODEL::findOrFail($id);
        return new EmployeeResource($employee);
    }

    public function storeEmployee(EmployeeRequest $request){
        EmployeeMODEL::create($request->validated());
        return response()->json("Employee Created!");
    }

    public function updateEmployee(Request $request, $id){
        $employee = EmployeeMODEL::findOrFail($id);
        $this->validate($request,[
            'employee_number'=> [
                'required',
                Rule::unique('tbl_employee')->ignore($employee->id)
            ],
            'name' => 'required',
            'position_id'=>'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $employee->update($input);
        return response()->json("Employee Updated!");
    }

    public function destroyEmployee($id){
        EmployeeMODEL::findorfail($id)->delete();
        return response()->json("Employee Archived!");
    }

    public function ArchivedEmployees(){
        $employee = EmployeeMODEL::orderBy('id')->onlyTrashed()->get();
        return $employee;
    }

    public function DestroyArchivedEmployee($id){
        EmployeeMODEL::onlyTrashed()->findorfail($id)->forcedelete();
        return response()->json("Employee Permanently Deleted!");
    }

    public function RestoreArchivedEmployee($id){
        EmployeeMODEL::onlyTrashed()->findorfail($id)->restore();
        return response()->json("Employee Successfully Restored!");
    }
}

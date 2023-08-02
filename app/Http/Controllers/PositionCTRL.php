<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Http\Resources\PositionResource;
use App\Models\PositionMODEL;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionCTRL extends Controller
{
    public function index(){
        $position = PositionMODEL::orderBy('id')->get();
        // return response()->json("Employee Informations");
        return new PositionResource($position);
    }

    public function show($id){
        $position = PositionMODEL::findOrFail($id);
        return new PositionResource($position);
    }

    public function store(PositionRequest $request){
        PositionMODEL::create($request->validated());
        return response()->json("Position Added!");
    }

    public function update(Request $request, $id){
        $position = PositionMODEL::findOrFail($id);
        $this->validate($request,[
            'position_name'=> [
                'required',
                Rule::unique('tbl_position')->ignore($position->id)
            ],
            'department' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        $position->update($input);
        return response()->json("Position Updated!");
    }

    public function destroy($id){
        PositionMODEL::findorfail($id)->delete();
        return response()->json("Position Archived!");
    }

    public function ArchivedPosition(){
        $position = PositionMODEL::orderBy('id')->onlyTrashed()->get();
        return $position;
    }

    public function DestroyArchivedPosition($id){
        PositionMODEL::onlyTrashed()->findorfail($id)->forcedelete();
        return response()->json("Position Permanently Deleted!");
    }

    public function RestoreArchivedPosition($id){
        PositionMODEL::onlyTrashed()->findorfail($id)->restore();
        return response()->json("Position Successfully Restored!");
    }
}

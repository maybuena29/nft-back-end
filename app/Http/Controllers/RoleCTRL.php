<?php

namespace App\Http\Controllers;

use App\Models\RoleMODEL;
use Illuminate\Http\Request;

class RoleCTRL extends Controller
{
    // SHOW ROLES
    public function showRoles(){
        $rolesList = RoleMODEL::orderBy('id')
        ->where('id', '!=' , 1)
        ->orWhereNull('id')
        ->select('id', 'role_name', 'permission', 'status')
        ->where("status", "active")->get();

        return response((array) [
            'data' => $rolesList,
            'status' => 'success'
        ], 200)
        ->header('Content-Type', 'application/json');
    }

}

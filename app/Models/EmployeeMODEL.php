<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMODEL extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_employee';
    protected $fillable = ['employee_number', 'name', 'position_id', 'status'];

    public function Position()
    {
        return $this->belongsTo(PositionMODEL::class, 'position_id', 'id');
    }

    public function Users()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}

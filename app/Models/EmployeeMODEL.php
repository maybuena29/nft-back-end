<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeMODEL extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_employee';
    protected $fillable = [
        'account_id',
        'firstname',
        'lastname',
        'contact',
        'address',
        'country',
        'state',
        'city',
        'zip_code',
        'department',
        'company',
        'role_id',
    ];

    public function Role()
    {
        return $this->belongsTo(RoleMODEL::class, 'role_id', 'id');
    }

    public function Users()
    {
        return $this->belongsTo(User::class, 'account_id', 'id');
    }
}

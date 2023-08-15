<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMODEL extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_roles';
    protected $fillable = ['role_name', 'permission', 'status'];

    public function getPermissionAttribute($value)
    {
        if(!$value){
            return $value;
        }
        return unserialize($value);
    }

}

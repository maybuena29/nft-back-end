<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionMODEL extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_position';
    protected $fillable = ['position_name', 'department', 'status'];
}

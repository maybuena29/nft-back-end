<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function Employee() {
        return $this->hasOne(EmployeeMODEL::class, 'id', 'account_id');
    }

}

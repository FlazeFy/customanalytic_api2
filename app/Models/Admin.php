<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    public $incrementing = false;

    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'fullname', 'email', 'password', 'created_at', 'updated_at'];

}

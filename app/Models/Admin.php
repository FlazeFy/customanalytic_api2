<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Admins",
 *     type="object",
 *     required={"id", "username", "fullname", "email", "password", "created_at", "updated_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="username", type="string", description="Unique Identifier for admin"),
 *     @OA\Property(property="fullname", type="string", description="Admin's fullname"),
 *     @OA\Property(property="email", type="string", description="Email for Auth and Task Scheduling"),
 *     @OA\Property(property="password", type="string", description="Sanctum Hashed Password"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the admin was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the admin profile was updated")
 * )
 */

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    public $incrementing = false;

    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'fullname', 'email', 'password', 'created_at', 'updated_at'];

}

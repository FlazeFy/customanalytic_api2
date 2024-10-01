<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Users",
 *     type="object",
 *     required={"id", "username", "fullname", "email", "role", "password", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="username", type="string", description="Unique Identifier for admin"),
 *     @OA\Property(property="fullname", type="string", description="Admin's fullname"),
 *     @OA\Property(property="role", type="string", description="User's role for get access to the apps feature"),
 *     @OA\Property(property="bio", type="string", description="User's bio"),
 *     @OA\Property(property="profile_img", type="string", description="Profile image of user"),
 *     @OA\Property(property="email", type="string", description="Email for Auth and Task Scheduling"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", description="Timestamp when the email is validated"),
 *     @OA\Property(property="password", type="string", description="Sanctum Hashed Password"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the user was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the user profile was updated")
 * )
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    public $incrementing = false;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'username', 'fullname', 'role', 'email', 'email_verified_at', 'password', 'created_at', 'updated_at'];
}

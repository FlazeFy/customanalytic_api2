<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Aircraft",
 *     type="object",
 *     required={"id", "name", "primary_role", "manufacturer", "country", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="name", type="string", description="Name of the aircraft"),
 *     @OA\Property(property="primary_role", type="string", description="The primary role of the aircraft"),
 *     @OA\Property(property="manufacturer", type="string", description="Manufacturer / Company of the aircraft"),
 *     @OA\Property(property="country", type="string", description="The country who invented the aircraft"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the aircraft data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the aircraft data was updated")
 * )
 */

class Aircraft extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    
    protected $table = 'aircraft';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'primary_role', 'manufacturer', 'country', 'created_at', 'updated_at'];
}

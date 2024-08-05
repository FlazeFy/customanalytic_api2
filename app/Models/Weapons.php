<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Weapons",
 *     type="object",
 *     required={"id", "name", "type", "country", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="name", type="string", description="Name of the weapon"),
 *     @OA\Property(property="type", type="string", description="Class of the weapon"),
 *     @OA\Property(property="country", type="string", description="Country of the weapon"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the weapon data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the weapon data was updated")
 * )
 */

class Weapons extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'weapons';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'type', 'country', 'created_at', 'updated_at'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Ships",
 *     type="object",
 *     required={"id", "name", "class", "country", "year", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="name", type="string", description="Name of the ship"),
 *     @OA\Property(property="class", type="string", description="Class of the ship"),
 *     @OA\Property(property="country", type="string", description="Country of the ship"),
 *     @OA\Property(property="year", type="integer", description="Year when the ships is launched"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the ship data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the ship data was updated")
 * )
 */

class Ships extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'ships';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'class', 'country', 'launch_year', 'created_at', 'updated_at'];
}

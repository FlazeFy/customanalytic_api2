<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Facilities",
 *     type="object",
 *     required={"id", "name", "type", "location", "country", "coordinate", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="name", type="string", description="Name of the facility"),
 *     @OA\Property(property="type", type="string", description="Type of the facility"),
 *     @OA\Property(property="location", type="string", description="Location name of the facility"),
 *     @OA\Property(property="country", type="string", description="Country of the facility located"),
 *     @OA\Property(property="coordinate", type="string", description="Date of event when the event was end"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the facility data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the facility data was updated")
 * )
 */

class Facilities extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type', 'location', 'country', 'coordinate'];
}

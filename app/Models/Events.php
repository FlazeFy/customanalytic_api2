<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Events",
 *     type="object",
 *     required={"id", "event", "date_start", "date_end", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="event", type="string", description="Name of the event"),
 *     @OA\Property(property="date_start", type="string", description="Date of event when the event was start"),
 *     @OA\Property(property="date_end", type="string", description="Date of event when the event was end"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the book data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the book data was updated")
 * )
 */

class Events extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'event', 'date', 'date_start', 'date_end', 'created_at','updated_at'];
}

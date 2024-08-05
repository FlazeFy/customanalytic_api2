<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Histories",
 *     type="object",
 *     required={"id", "history_type", "body", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="history_type", type="string", description="Id of the story"),
 *     @OA\Property(property="body", type="string", description="The body of feedback"),
 *     @OA\Property(property="rate", type="integer", description="Rate of the feedback"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the feedback data was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="Id of the user who created the history")
 * )
 */

class Histories extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'histories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','history_type', 'body', 'created_at', 'created_by'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Feedbacks",
 *     type="object",
 *     required={"id", "stories_id", "body", "rate", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="stories_id", type="string", description="Id of the story"),
 *     @OA\Property(property="body", type="string", description="The body of feedback"),
 *     @OA\Property(property="rate", type="integer", description="Rate of the feedback"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the feedback data was created"),
 *     @OA\Property(property="created_by", type="string", format="uuid", description="Id of user who created the feedback")
 * )
 */

class Feedbacks extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'feedbacks';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'stories_id', 'body', 'rate', 'created_at', 'created_by'];
}

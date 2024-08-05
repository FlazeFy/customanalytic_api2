<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Discussions",
 *     type="object",
 *     required={"id", "stories_id", "body", "reviewer", "review_date", "created_at", "created_by"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="stories_id", type="string", description="Id of the story"),
 *     @OA\Property(property="reply_id", type="string", description="Id of the other discussion who got replied"),
 *     @OA\Property(property="body", type="string", description="The body of the message"),
 *     @OA\Property(property="attachment", type="string", format="date", description="Detail of attachment attached in the discussion. Contain attachment url, attachment type, and attachment name"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the story data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the story data was updated"),
 *     @OA\Property(property="created_by", type="string", format="uuid",description="ID of the user / admin who created the discussion"),
 *     @OA\Property(property="updated_by", type="string", format="uuid",description="ID of the user / admin who updated the discussion")
 * )
 */

class Discussions extends Model
{
    use HasFactory;

    protected $table = 'discussions';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'stories_id', 'reply_id', 'body', 'attachment', 'created_at', 'created_by', 'updated_at', 'updated_by'];
}

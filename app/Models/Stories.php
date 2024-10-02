<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Stories",
 *     type="object",
 *     required={"id", "slug_name", "main_title", "is_finished", "story_type", "date_start", "story_location", "story_tag", "story_detail", "story_reference", "created_at", "created_by"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="slug_name", type="string", description="Slug name of the story"),
 *     @OA\Property(property="main_title", type="string", description="Main title of the story"),
 *     @OA\Property(property="is_finished", type="boolean", description="Indicates if the story is finished"),
 *     @OA\Property(property="story_type", type="string", description="Type of the story"),
 *     @OA\Property(property="date_start", type="string", format="date", description="Date when the story was started"),
 *     @OA\Property(property="date_end", type="string", format="date", description="Date when the story was end"),
 *     @OA\Property(property="story_result", type="array", @OA\Items(type="object"), description="Result of the story"),
 *     @OA\Property(property="story_location", type="string", description="Location of the story"),
 *     @OA\Property(property="story_tag", type="array", @OA\Items(type="object"), description="Tags associated with the story"),
 *     @OA\Property(property="story_detail", type="string", description="Detailed description of the story"),
 *     @OA\Property(property="story_stats", type="array", @OA\Items(type="object"), description="Statistics related to the story"),
 *     @OA\Property(property="story_reference", type="array", @OA\Items(type="object"), description="References for the story"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the story was created"),
 *     @OA\Property(property="created_by", type="string", description="User who created the story"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the story was last updated"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", description="Timestamp when the story was deleted"),
 * )
 */

class Stories extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'stories';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'slug_name', 'main_title', 'is_finished', 'story_type', 'date_start', 'date_end', 'story_result', 'story_location', 'story_tag', 'story_detail', 'story_stats', 'story_reference', 'created_at', 'created_by', 'updated_at', 'deleted_at'];
    protected $casts = [
        'story_tag' => 'array',
        'story_stats' => 'array',
        'story_reference' => 'array',
        'story_result' => 'array'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stories extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $table = 'stories';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'main_title', 'is_finished', 'story_type', 'date_start', 'date_end', 'story_result', 'story_location', 'story_tag', 'story_detail', 'story_stats', 'story_reference', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];
}

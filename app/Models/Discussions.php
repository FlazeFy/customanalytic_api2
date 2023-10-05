<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussions extends Model
{
    use HasFactory;

    protected $table = 'discussions';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'stories_id', 'reply_id', 'body', 'attachment', 'created_at', 'created_by', 'updated_at', 'updated_by'];
}

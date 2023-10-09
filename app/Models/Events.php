<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'event', 'date', 'date_start', 'date_end', 'created_at', 'created_by','updated_at', 'updated_by'];
}

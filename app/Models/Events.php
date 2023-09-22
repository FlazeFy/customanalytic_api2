<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $fillable = ['event', 'date'];
}

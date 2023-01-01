<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facilities extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type', 'location', 'country'];
}

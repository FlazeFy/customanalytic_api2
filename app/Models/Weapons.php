<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapons extends Model
{
    use HasFactory;

    protected $table = 'weapons';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'type', 'country'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapons extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'weapons';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'type', 'country'];
}

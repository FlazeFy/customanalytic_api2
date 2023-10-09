<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weapons extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'weapons';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'type', 'country', 'created_at', 'created_by', 'updated_at', 'updated_by'];
}

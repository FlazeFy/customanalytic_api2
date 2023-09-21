<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ships extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'ships';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'class', 'country', 'launch_year'];
}

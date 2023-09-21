<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'primary_role', 'manufacturer', 'country'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $table = 'aircraft';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'primary_role', 'manufacturer', 'country'];
}

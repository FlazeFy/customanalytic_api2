<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aircraft extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;
    
    protected $table = 'aircraft';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'primary_role', 'manufacturer', 'country', 'created_at', 'created_by', 'updated_at', 'updated_by'];
}

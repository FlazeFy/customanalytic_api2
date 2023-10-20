<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histories extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'histories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','history_type', 'body', 'created_at', 'created_by'];
}

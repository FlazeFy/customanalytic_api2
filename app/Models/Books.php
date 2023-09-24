<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'title', 'author', 'reviewer', 'review_date', 'datetime'];
}

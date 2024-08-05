<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Books",
 *     type="object",
 *     required={"id", "title", "author", "reviewer", "review_date", "created_at"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="title", type="string", description="Title of the book"),
 *     @OA\Property(property="author", type="string", description="Name of the book's author"),
 *     @OA\Property(property="reviewer", type="string", description="Name of the book's reviewer"),
 *     @OA\Property(property="review_date", type="string", format="date", description="Date when the book is reviewed"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the book data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the book data was updated")
 * )
 */

class Books extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $incrementing = false;

    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'title', 'author', 'reviewer', 'review_date', 'created_at','updated_at'];
}

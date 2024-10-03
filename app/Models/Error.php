<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     required={"id", "message", "stack_trace", "file", "line", "created_at"},
 * 
 *     @OA\Property(property="id", type="integer", description="Primary Key"),
 *     @OA\Property(property="message", type="string", description="Message or description of the error"),
 *     @OA\Property(property="stack_trace", type="string", description="Trail of function calls leading up to the error"),
 *     @OA\Property(property="file", type="string", description="Path of the file where the error is faced"),
 *     @OA\Property(property="line", type="integer", description="Line of code in specific file where the error is faced"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the error was faced"),
 *     @OA\Property(property="fixed_at", type="string", format="date-time", description="Timestamp when the dev team finally fix and deploy the bug / error fix"),
 *     @OA\Property(property="faced_by", type="string", format="uuid", description="ID of the user who specific faced the error"),
 * )
 */

class Error extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'errors';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'message', 'stack_trace', 'file', 'line', 'faced_by', 'created_at'];
}

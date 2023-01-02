<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casualities extends Model
{
    use HasFactory;

    protected $table = 'casualities';
    protected $primaryKey = 'id';
    protected $fillable = ['country', 'continent', 'total_population', 'military_death', 'civilian_death', 'total_death', 'death_per_pop', 'avg_death_per_pop', 'military_wounded'];
}

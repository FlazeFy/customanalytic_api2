<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Casualities",
 *     type="object",
 *     required={"id", "country", "continent", "total_population", "military_death", "civilian_death", "death_per_pop", "avg_death_per_pop"},
 * 
 *     @OA\Property(property="id", type="string", format="uuid", description="Primary Key"),
 *     @OA\Property(property="country", type="string", description="Name of the country"),
 *     @OA\Property(property="continent", type="string", description="Continent where the country is located"),
 *     @OA\Property(property="total_population", type="string", description="Total population of the country"),
 *     @OA\Property(property="military_death", type="integer", description="Number of military deaths"),
 *     @OA\Property(property="civilian_death", type="integer", description="Number of civilian deaths"),
 *     @OA\Property(property="death_per_pop", type="number", format="float", description="Death per population ratio"),
 *     @OA\Property(property="avg_death_per_pop", type="number", format="float", description="Average death per population ratio"),
 *     @OA\Property(property="military_wounded", type="integer", description="Number of military wounded"),
 * 
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the casualties data was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the casualties data was updated")
 * )
 */

class Casualities extends Model
{
    use HasFactory;

    protected $table = 'casualities';
    protected $primaryKey = 'id';
    protected $fillable = ['country', 'continent', 'total_population', 'military_death', 'civilian_death', 'death_per_pop', 'avg_death_per_pop', 'military_wounded'];
}

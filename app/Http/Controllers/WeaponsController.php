<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Weapons;

class WeaponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAllWeapons($page_limit, $order){
        $wpn = Weapons::select('id', 'name', 'type', 'country')
            ->orderBy('name', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getWeaponsSummary(){
        $wpn = Weapons::selectRaw("type as most_produced, count(*) as 'total', 
                (SELECT GROUP_CONCAT(' ',country)
                FROM (
                    SELECT country 
                    FROM weapons WHERE type = 'Field Gun '
                    GROUP BY country
                    ORDER BY count(id) DESC LIMIT 3
                ) q) most_produced_by_country, 
                (SELECT CAST(AVG(total) as int) 
                FROM (
                    SELECT COUNT(*) as total
                    FROM weapons
                    WHERE type = 'Field Gun '
                    GROUP BY country
                ) q) AS average_by_country
                ")
            ->groupBy('type')
            ->orderBy('total', 'DESC')
            ->limit(1)
            ->get();

        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getTotalWeaponsByType(){
        $wpn = Weapons::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getTotalWeaponsByCountry(){
        $wpn = Weapons::selectRaw('country, count(*) as total')
            ->groupBy('country')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getTotalWeaponsBySides(){
        $shp = Weapons::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
            OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS side, COUNT(*) as total')
            ->groupBy('side')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }
}

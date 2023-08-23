<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Vehicles;

class VehiclesController extends Controller
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

    public function getAllVehicles($page_limit, $order){
        $vhc = Vehicles::select('id', 'name', 'primary_role', 'manufacturer', 'country')
            ->orderBy('name', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
        ]);
    }

    public function getVehiclesSummary(){
        $vch = Vehicles::selectRaw("primary_role as most_produced, count(*) as 'total', 
                (SELECT GROUP_CONCAT(' ',country)
                FROM (
                    SELECT country 
                    FROM vehicles 
                    WHERE primary_role = 'Light Tank'
                    GROUP BY country
                    ORDER BY count(id) DESC LIMIT 3
                ) q) most_produced_by_country, 
                (SELECT CAST(AVG(total) as int) 
                FROM (
                    SELECT COUNT(*) as total
                    FROM vehicles
                    WHERE primary_role = 'Light Tank'
                    GROUP BY country
                ) q) AS average_by_country
                ")
            ->groupBy('primary_role')
            ->orderBy('total', 'DESC')
            ->limit(1)
            ->get();

        return response()->json([
            "msg"=> count($vch)." Data retrived", 
            "status"=> 200,
            "data"=> $vch
        ]);
    }


    public function getTotalVehiclesByRole(){
        $vhc = Vehicles::selectRaw('primary_role as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
        ]);
    }

    public function getTotalVehiclesByCountry(){
        $vhc = Vehicles::selectRaw('country as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
        ]);
    }

    public function getTotalVehiclesBySides(){
        $vhc = Vehicles::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
            OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
            ->groupByRaw('1')
            ->get();
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
        ]);
    }
}

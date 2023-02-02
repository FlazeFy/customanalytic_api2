<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Facilities;

class FacilitiesController extends Controller
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

    public function getFacilitiesSummary(){
        $fac = Facilities::selectRaw("type as most_built, count(*) as 'total', 
                (SELECT GROUP_CONCAT(' ',country)
                FROM (
                    SELECT country 
                    FROM facilities 
                    WHERE type = 'Airfield '
                    GROUP BY country
                    ORDER BY count(id) DESC LIMIT 3
                ) q) most_built_by_country, 
                (SELECT CAST(AVG(total) as int) 
                FROM (
                    SELECT COUNT(*) as total
                    FROM facilities
                    WHERE type = 'Airfield '
                    GROUP BY country
                ) q) AS average_by_country
                ")
            ->groupBy('type')
            ->orderBy('total', 'DESC')
            ->limit(1)
            ->get();

        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=> 200,
            "data"=> $fac
        ]);
    }

    public function getTotalFacilitiesByType(){
        $fac = Facilities::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=>200,
            "data"=>$fac
        ]);
    }

    public function getTotalFacilitiesByCountry(){
        $fac = Facilities::selectRaw('country, count(*) as total')
            ->groupBy('country')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=>200,
            "data"=>$fac
        ]);
    }

    public function getFacilitiesByLocation($type){
        if($type != "NULL"){
            $fac = Facilities::selectRaw('name, type, location, country, coordinate')
                ->where('coordinate', '!=', '')
                ->where('type', $type)
                ->get();
        } else {
            $fac = Facilities::selectRaw('name, type, location, country, coordinate')
                ->where('coordinate', '!=', '')
                ->get();
        }
    
        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=>200,
            "data"=>$fac
        ]);
    }

    public function getTotalFacilitiesBySides(){
        $fac = Facilities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
            OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS side, COUNT(*) as total')
            ->groupBy('side')
            ->get();
    
        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=> 200,
            "data"=> $fac
        ]);
    }

    public function getFacilitiesType(){
        $fac = Facilities::selectRaw('type')
            ->groupBy('type')
            ->get();
    
        return response()->json([
            "msg"=> count($fac)." Data retrived", 
            "status"=>200,
            "data"=>$fac
        ]);
    }
}

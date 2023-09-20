<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Casualities;

class CasualitiesController extends Controller
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

    public function getAllCasualities($page_limit, $orderby, $ordertype){
        $cst = Casualities::select('id', 'country', 'continent', 'total_population', 'military_death', 'civilian_death', 'total_death', 'death_per_pop', 'avg_death_per_pop', 'military_wounded')
            ->orderBy($orderby, $ordertype)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($cst)." Data retrived", 
            "status"=>200,
            "data"=>$cst
        ]);
    }

    public function getTotalDeathByCountry($order, $page_limit){
        if($order != "NULL"){
            $cst = Casualities::selectRaw('country as context, total_population, military_death, civilian_death, military_death + civilian_death as total')
                ->whereRaw('military_death+civilian_death != 0')
                ->orderBy("total", $order)
                ->paginate($page_limit);
        } else {
            $cst = Casualities::selectRaw('country as context, total_population, military_death, civilian_death, military_death + civilian_death as total')
                ->whereRaw('military_death+civilian_death != 0')
                ->paginate($page_limit);
        }   
    
        return response()->json([
            "msg"=> count($cst)." Data retrived", 
            "status"=>200,
            "data"=>$cst
        ]);
    }

    public function getCasualitiesSummary(){
        $cst = Casualities::selectRaw("(SELECT cast(avg(military_death + civilian_death) as decimal(10,0)) from casualities) as average_death, 
                max(military_death + civilian_death) as 'highest_death', 
                country as highest_death_country, 
                cast((max(military_death + civilian_death) / (SELECT sum(military_death + civilian_death) from casualities) * 100) as decimal(10,2)) as highest_death_country_percent,
                (SELECT sum(military_death + civilian_death) from casualities) as total_death_all")
            ->whereRaw('military_death + civilian_death = ( SELECT MAX(military_death + civilian_death) FROM casualities)')
            ->groupBy('military_death', 'civilian_death', 'country')
            ->get();

        return response()->json([
            "msg"=> count($cst)." Data retrived", 
            "status"=>200,
            "data"=>$cst
        ]);
    }

    public function getTotalDeathBySides($view){
        if($view == "military" || $view == "civilian"){
            $cst = Casualities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                    OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                    OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, sum('.$view.'_death) as total')
                ->groupByRaw("1")
                ->get();
        
            return response()->json([
                "msg"=> count($cst)." Data retrived", 
                "status"=>200,
                "data"=>$cst
            ]);
        } else {
            return response()->json([
                "msg"=> "view not found", 
                "status"=>200,
                "data"=>null
            ]);
        }
    }
}
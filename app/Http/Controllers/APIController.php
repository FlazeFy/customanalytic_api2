<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Aircraft;
use App\Models\Ships;
use App\Models\Vehicles;
use App\Models\Facilities;
use App\Models\Weapons;
use App\Models\Events;
use App\Models\Books;
use App\Models\Casualities;

class APIController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function getAllAircraft($page_limit, $order){
        $air = Aircraft::select('id', 'name', 'primary_role', 'manufacturer', 'country')
            ->orderBy('name', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getAllShips($page_limit, $order){
        $shp = Ships::select('id', 'name', 'class', 'country', 'launch_year')
            ->orderBy('name', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
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

    public function getAllEvents($page_limit, $order){
        $evt = Events::select('id', 'event', 'date')
            ->orderBy('event', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($evt)." Data retrived", 
            "status"=>200,
            "data"=>$evt
        ]);
    }

    public function getAllBooks($page_limit, $order){
        $bok = Books::select('id', 'title', 'author', 'reviewer', 'review_date', 'datetime')
            ->orderBy('title', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
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
            $cst = Casualities::selectRaw('country, total_population, military_death, civilian_death, military_death + civilian_death as total')
                ->whereRaw('military_death+civilian_death != 0')
                ->orderBy("total", $order)
                ->paginate($page_limit);
        } else {
            $cst = Casualities::selectRaw('country, total_population, military_death, civilian_death, military_death + civilian_death as total')
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

    public function getAircraftSummary(){
        $air = Aircraft::selectRaw("primary_role as most_produced, count(*) as 'total', 
                (SELECT GROUP_CONCAT(' ',country)
                FROM (
                    SELECT country 
                    FROM aircraft 
                    WHERE primary_role = 'Fighter'
                    GROUP BY country
                    ORDER BY count(id) DESC LIMIT 3
                ) q) most_produced_by_country, 
                (SELECT CAST(AVG(total) as int) 
                FROM (
                    SELECT COUNT(*) as total
                    FROM aircraft
                    WHERE primary_role = 'Fighter'
                    GROUP BY country
                ) q) AS average_by_country
                ")
            ->groupBy('primary_role')
            ->orderBy('total', 'DESC')
            ->limit(1)
            ->get();

        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=> 200,
            "data"=> $air
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

    public function getTotalDeathBySides(){
        $cst = Casualities::selectRaw('sum(military_death) as m_death, sum(civilian_death) as c_death,
                (CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS side')
                ->groupBy("side")
                ->get();
    
        return response()->json([
            "msg"=> count($cst)." Data retrived", 
            "status"=>200,
            "data"=>$cst
        ]);
    }

    public function getTotalAircraftByRole(){
        $air = Aircraft::selectRaw('primary_role, count(*) as total')
            ->groupBy('primary_role')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getTotalAircraftBySides(){
        $air = Aircraft::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
            OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS side, COUNT(*) as total')
            ->groupBy('side')
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getTotalAircraftByCountry(){
        $air = Aircraft::selectRaw('country, count(*) as total')
            ->groupBy('country')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getTotalShipsByClass(){
        $shp = Ships::selectRaw('class, count(*) as total')
            ->groupBy('class')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }

    public function getTotalShipsByCountry(){
        $shp = Ships::selectRaw('country, count(*) as total')
            ->groupBy('country')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }

    public function getTotalShipsBySides(){
        $shp = Ships::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
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

    public function getTotalShipsByLaunchYear(){
        $shp = Ships::selectRaw('launch_year, count(*) as total')
            ->where('launch_year', '!=', 0000)
            ->whereRaw('char_length(launch_year) = 5')
            ->groupBy('launch_year')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }

    public function getTotalVehiclesByRole(){
        $vhc = Vehicles::selectRaw('primary_role, count(*) as total')
            ->groupBy('primary_role')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
        ]);
    }

    public function getTotalVehiclesByCountry(){
        $vhc = Vehicles::selectRaw('country, count(*) as total')
            ->groupBy('country')
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
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS side, COUNT(*) as total')
            ->groupBy('side')
            ->get();
    
        return response()->json([
            "msg"=> count($vhc)." Data retrived", 
            "status"=>200,
            "data"=>$vhc
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

    public function getTotalBooksByReviewer(){
        $bok = Books::selectRaw('reviewer, count(*) as total')
            ->groupBy('reviewer')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }

    public function getTotalBooksByYearReview(){
        $bok = Books::selectRaw('YEAR(datetime) as year_review, count(*) as total')
            ->whereRaw('YEAR(datetime) is not null')
            ->groupBy('year_review')
            ->orderBy('year_review', 'ASC')
            ->get();
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }
}

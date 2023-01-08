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

    public function getTotalShipsByLaunchYear(){
        $shp = Ships::selectRaw('launch_year, count(*) as total')
            ->where('launch_year', '!=', 0000)
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

    public function getTotalFacilitiesByLocation(){
        $fac = Facilities::selectRaw('name, location, country, coordinate')
            ->where('coordinate', '!=', '')
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
}

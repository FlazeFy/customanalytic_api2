<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Aircraft;
use App\Models\Ships;
use App\Models\Vehicles;
use App\Models\Facilities;

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
}

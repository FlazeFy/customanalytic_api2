<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validation;

use App\Models\Aircraft;

class AircraftController extends Controller
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

    public function getTotalAircraftByRole($limit){
        $air = Aircraft::selectRaw('primary_role as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getTotalAircraftByManufacturer($limit){
        $air = Aircraft::selectRaw('manufacturer as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
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
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
            ->groupByRaw('1')
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function getTotalAircraftByCountry($limit){
        $air = Aircraft::selectRaw('country as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    
        return response()->json([
            "msg"=> count($air)." Data retrived", 
            "status"=>200,
            "data"=>$air
        ]);
    }

    public function updateAircraftById(Request $request, $id){
        $validator = Validation::getValidateAircraft($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            Aircraft::where('id', $id)->update([
                'name' => $request->name,
                'primary_role' => $request->primary_role,
                'manufacturer' => $request->manufacturer,
                'country' => $request->country,
            ]);
    
            return response()->json([
                "msg" => "'".$request->name."' Data Updated", 
                "status" => 200
            ]);
        }
    }

    public function deleteAircraftById($id){
        $air = Aircraft::selectRaw("concat (name, ' - ', primary_role) as final_name")
            ->where('id', $id)
            ->first();
        Aircraft::destroy($id);

        return response()->json([
            "msg"=> " '".$air->final_name."' Data Destroyed", 
            "status"=>200
        ]);
    }
}

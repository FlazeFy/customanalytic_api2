<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validation;

use App\Models\Ships;

class ShipsController extends Controller
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

    public function getShipsSummary(){
        $shp = Ships::selectRaw("class as most_produced, count(*) as 'total', 
                (SELECT GROUP_CONCAT(' ',country)
                FROM (
                    SELECT country 
                    FROM ships 
                    WHERE class = 'M-class Minesweeper '
                    GROUP BY country
                    ORDER BY count(id) DESC LIMIT 3
                ) q) most_produced_by_country, 
                (SELECT CAST(AVG(total) as int) 
                FROM (
                    SELECT COUNT(*) as total
                    FROM ships
                    WHERE class = 'M-class Minesweeper '
                    GROUP BY country
                ) q) AS average_by_country,
                (SELECT GROUP_CONCAT(' ',launch_year)
                FROM (
                    SELECT launch_year
                    FROM ships 
                    WHERE launch_year != '0000 ' 
                    AND char_length(launch_year) = 5 
                    GROUP BY launch_year
                    ORDER BY count(id) DESC LIMIT 3 
                ) q) most_built_year
                ")
            ->groupBy('class')
            ->orderBy('total', 'DESC')
            ->limit(1)
            ->get();

        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=> 200,
            "data"=> $shp
        ]);
    }

    public function getTotalShipsByClass(){
        $shp = Ships::selectRaw('class as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }

    public function getTotalShipsByCountry($limit){
        $shp = Ships::selectRaw('country as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
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
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
            ->groupByRaw('1')
            ->get();
    
        return response()->json([
            "msg"=> count($shp)." Data retrived", 
            "status"=>200,
            "data"=>$shp
        ]);
    }

    public function getTotalShipsByLaunchYear(){
        $shp = Ships::selectRaw('launch_year as context, count(*) as total')
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

    public function updateShipById(Request $request, $id){
        $validator = Validation::getValidateShips($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            Ships::where('id', $id)->update([
                'name' => $request->name,
                'class' => $request->class,
                'country' => $request->country,
                'launch_year' => $request->launch_year,
            ]);
    
            return response()->json([
                "msg" => "'".$request->name."' Data Updated", 
                "status" => 200
            ]);
        }
    }

    public function deleteShipById($id){
        $shp = Ships::selectRaw("concat (name, ' - ', class) as final_name")
            ->where('id', $id)
            ->first();
            Ships::destroy($id);

        return response()->json([
            "msg"=> " '".$shp->final_name."' Data Destroyed", 
            "status"=>200
        ]);
    }
}

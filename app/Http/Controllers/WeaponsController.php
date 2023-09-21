<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validation;

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

    public function getTotalWeaponsByType($limit){
        $wpn = Weapons::selectRaw('type as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getTotalWeaponsByCountry($limit){
        $wpn = Weapons::selectRaw('country as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function getTotalWeaponsBySides(){
        $wpn = Weapons::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
            OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
            OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
            ->groupByRaw('1')
            ->get();
    
        return response()->json([
            "msg"=> count($wpn)." Data retrived", 
            "status"=>200,
            "data"=>$wpn
        ]);
    }

    public function updateWeaponById(Request $request, $id){
        $validator = Validation::getValidateWeapon($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            Weapons::where('id', $id)->update([
                'name' => $request->name,
                'type' => $request->type,
                'country' => $request->country,
            ]);
    
            return response()->json([
                "msg" => "'".$request->name."' Data Updated", 
                "status" => 200
            ]);
        }
    }

    public function deleteWeaponById($id){
        $wpn = Weapons::selectRaw("concat (name, ' - ', type) as final_name")
            ->where('id', $id)
            ->first();
            Weapons::destroy($id);

        return response()->json([
            "msg"=> " '".$wpn->final_name."' Data Destroyed", 
            "status"=>200
        ]);
    }
}

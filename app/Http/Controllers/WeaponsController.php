<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Weapons;

class WeaponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createWeapon(Request $request)
    {
        try {
            $validator = Validation::getValidateWeapon($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Weapons::selectRaw('1')->where('name', $request->name)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Weapons::create([
                        'id' => $uuid,
                        'name' => $request->name,
                        'type' => $request->type,
                        'country' => $request->country,
                    ]);
            
                    return response()->json([
                        'message' => "'".$request->name."' Data Created", 
                        'status' => 'success'
                    ], Response::HTTP_OK);
                }else{
                    return response()->json([
                        "message" => "Data is already exist", 
                        "status" => 'failed'
                    ], Response::HTTP_CONFLICT);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllWeapons($page_limit, $order){
        try {
            $wpn = Weapons::select('id', 'name', 'type', 'country')
                ->orderBy('name', $order)
                ->paginate($page_limit);
        
            return response()->json([
                'message' => count($wpn)." Data retrived", 
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getWeaponsSummary(){
        try {
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
                'message' => count($wpn)." Data retrived", 
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalWeaponsByType($limit){
        try {
            $wpn = Weapons::selectRaw('type as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($wpn)." Data retrived", 
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalWeaponsByCountry($limit){
        try {
            $wpn = Weapons::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($wpn)." Data retrived", 
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalWeaponsBySides(){
        try {
            $wpn = Weapons::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                'message' => count($wpn)." Data retrived", 
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateWeaponById(Request $request, $id){
        try {
            $validator = Validation::getValidateWeapon($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                Weapons::where('id', $id)->update([
                    'name' => $request->name,
                    'type' => $request->type,
                    'country' => $request->country,
                ]);
        
                return response()->json([
                    "message" => "'".$request->name."' Data Updated", 
                    "status" => 'success'
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteWeaponById($id){
        try {
            $wpn = Weapons::selectRaw("concat (name, ' - ', type) as final_name")
                ->where('id', $id)
                ->first();

            Weapons::destroy($id);

            return response()->json([
                'message' => " '".$wpn->final_name."' Data Destroyed", 
                'status' => 'success'
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

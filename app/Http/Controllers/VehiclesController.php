<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Vehicles;

class VehiclesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createVehicles(Request $request)
    {
        try {
            $validator = Validation::getValidateVehicle($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "msg" => $errors, 
                    "status" => 422
                ]);
            } else {
                $check = Vehicles::selectRaw('1')->where('name', $request->name)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Vehicles::create([
                        'id' => $uuid,
                        'name' => $request->name,
                        'primary_role' => $request->primary_role,
                        'manufacturer' => $request->manufacturer,
                        'country' => $request->country,
                    ]);
            
                    return response()->json([
                        "msg" => "'".$request->name."' Data Created", 
                        "status" => 200
                    ]);
                }else{
                    return response()->json([
                        "msg" => "Data is already exist", 
                        "status" => 422
                    ]);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllVehicles($page_limit, $order){
        try {
            $vhc = Vehicles::select('id', 'name', 'primary_role', 'manufacturer', 'country')
                ->orderBy('name', $order)
                ->paginate($page_limit);
        
            return response()->json([
                'message' => count($vhc)." Data retrived", 
                "status"=>200,
                "data"=>$vhc
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getVehiclesSummary(){
        try {
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
                'message' => count($vch)." Data retrived", 
                "status"=> 200,
                "data"=> $vch
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getTotalVehiclesByRole($limit){
        try {
            $vhc = Vehicles::selectRaw('primary_role as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($vhc)." Data retrived", 
                "status"=>200,
                "data"=>$vhc
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalVehiclesByCountry($limit){
        try {
            $vhc = Vehicles::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($vhc)." Data retrived", 
                "status"=>200,
                "data"=>$vhc
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalVehiclesBySides(){
        try {
            $vhc = Vehicles::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                'message' => count($vhc)." Data retrived", 
                "status"=>200,
                "data"=>$vhc
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateVehicleById(Request $request, $id){
        try {
            $validator = Validation::getValidateVehicle($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "msg" => $errors, 
                    "status" => 422
                ]);
            } else {
                Vehicles::where('id', $id)->update([
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
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteVehiclesById($id){
        try {
            $vhc = Vehicles::selectRaw("concat (name, ' - ', primary_role) as final_name")
                ->where('id', $id)
                ->first();
                Vehicles::destroy($id);

            return response()->json([
                'message' => " '".$vhc->final_name."' Data Destroyed", 
                "status"=>200
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

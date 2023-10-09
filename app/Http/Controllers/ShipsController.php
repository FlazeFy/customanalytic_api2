<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Ships;

class ShipsController extends Controller
{
    public function createShip(Request $request)
    {
        try {
            $validator = Validation::getValidateShips($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Ships::selectRaw('2')->where('name', $request->name)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Ships::create([
                        'id' => $uuid,
                        'name' => $request->name,
                        'class' => $request->class,
                        'country' => $request->country,
                        'launch_year' => $request->launch_year,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => "1",
                        'updated_at' => null,
                        'updated_by' => null,
                    ]);
            
                    return response()->json([
                        "message" => Generator::getMessageTemplate("api_create", "ship", $request->name), 
                        "status" => 'success'
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

    public function getAllShips($page_limit, $order, $search){
        try {
            $search = trim($search);

            $shp = Ships::select('id', 'name', 'class', 'country', 'launch_year')
                ->orderBy('name', $order);

            // Filtering
            if($search != "" && $search != "%20"){
                $shp = $shp->where('name', 'LIKE', '%' . $search . '%');
            }

            $shp = $shp->paginate($page_limit);
        
            return response()->json([
                //'message' => count($shp)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                'status' => 'success',
                'data' => $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getShipsSummary(){
        try {
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
                //'message' => count($shp)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                "status"=> 'success',
                "data"=> $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalShipsByClass(){
        try {
            $shp = Ships::selectRaw('class as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->get();
        
            return response()->json([
                'message' => count($shp)." Data retrived", 
                'status' => 'success',
                'data' => $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalShipsByCountry($limit){
        try {
            $shp = Ships::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                //'message' => count($shp)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                'status' => 'success',
                'data' => $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalShipsBySides(){
        try {
            $shp = Ships::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                //'message' => count($shp)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                'status' => 'success',
                'data' => $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalShipsByLaunchYear(){
        try {
            $shp = Ships::selectRaw('launch_year as context, count(*) as total')
                ->where('launch_year', '!=', 0000)
                ->whereRaw('char_length(launch_year) = 5')
                ->groupBy('launch_year')
                ->orderBy('total', 'DESC')
                ->get();
        
            return response()->json([
                //'message' => count($shp)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                'status' => 'success',
                'data' => $shp
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateShipById(Request $request, $id){
        try {
            $validator = Validation::getValidateShips($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'message' => $errors, 
                    'status' => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                Ships::where('id', $id)->update([
                    'name' => $request->name,
                    'class' => $request->class,
                    'country' => $request->country,
                    'launch_year' => $request->launch_year,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => null,
                ]);
        
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_update", "ship", $request->name), 
                    'status' => 'success'
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteShipById($id){
        try {
            $shp = Ships::selectRaw("concat (name, ' - ', class) as final_name")
                ->where('id', $id)
                ->first();

            Ships::destroy($id);

            return response()->json([
                'message' => Generator::getMessageTemplate("api_delete", "airplane", $shp->final_name), 
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

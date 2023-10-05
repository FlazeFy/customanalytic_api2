<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Aircraft;

class AircraftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAircraft(Request $request)
    {
        try {
            $validator = Validation::getValidateAircraft($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Aircraft::selectRaw('1')->where('name', $request->name)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Aircraft::create([
                        'id' => $uuid,
                        'name' => $request->name,
                        'primary_role' => $request->primary_role,
                        'manufacturer' => $request->manufacturer,
                        'country' => $request->country,
                    ]);
            
                    return response()->json([
                        "message" => "'".$request->name."' Data Created", 
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

    public function getAllAircraft($page_limit, $order, $search){
        try {
            $search = trim($search);

            $air = Aircraft::select('id', 'name', 'primary_role', 'manufacturer', 'country')
                ->orderBy('name', $order);

            // Filtering
            if($search != "" && $search != "%20"){
                $air = $air->where('name', 'LIKE', '%' . $search . '%')
                    ->orwhere('manufacturer', 'LIKE', '%' . $search . '%');
            }

            $air = $air->paginate($page_limit);
        
            return response()->json([
                'message' => count($air)." Data retrived", 
                "status" => 'success',
                "data" => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAircraftSummary(){
        try {
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
                'message' => count($air)." Data retrived", 
                "status" => 'success',
                "data" => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalAircraftByRole($limit){
        try {
            $air = Aircraft::selectRaw('primary_role as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($air)." Data retrived", 
                "status" => 'success',
                "data" => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalAircraftByManufacturer($limit){
        try {
            $air = Aircraft::selectRaw('manufacturer as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($air)." Data retrived", 
                "status" => 'success',
                "data" => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalAircraftBySides(){
        try {
            $air = Aircraft::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                'message' => count($air)." Data retrived", 
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalAircraftByCountry($limit){
        try {
            $air = Aircraft::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($air)." Data retrived", 
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateAircraftById(Request $request, $id){
        try {
            $validator = Validation::getValidateAircraft($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                Aircraft::where('id', $id)->update([
                    'name' => $request->name,
                    'primary_role' => $request->primary_role,
                    'manufacturer' => $request->manufacturer,
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

    public function deleteAircraftById($id){
        try {
            $air = Aircraft::selectRaw("concat (name, ' - ', primary_role) as final_name")
                ->where('id', $id)
                ->first();
                
            Aircraft::destroy($id);

            return response()->json([
                'message' => " '".$air->final_name."' Data Destroyed", 
                "status" => 'success'
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

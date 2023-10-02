<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Facilities;

class FacilitiesController extends Controller
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

    public function getFacilitiesSummary(){
        try {
            $fac = Facilities::selectRaw("type as most_built, count(*) as 'total', 
                    (SELECT GROUP_CONCAT(' ',country)
                    FROM (
                        SELECT country 
                        FROM facilities 
                        WHERE type = 'Airfield '
                        GROUP BY country
                        ORDER BY count(id) DESC LIMIT 3
                    ) q) most_built_by_country, 
                    (SELECT CAST(AVG(total) as int) 
                    FROM (
                        SELECT COUNT(*) as total
                        FROM facilities
                        WHERE type = 'Airfield '
                        GROUP BY country
                    ) q) AS average_by_country
                    ")
                ->groupBy('type')
                ->orderBy('total', 'DESC')
                ->limit(1)
                ->get();

            return response()->json([
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalFacilitiesByType($limit){
        try {
            $fac = Facilities::selectRaw('type as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalFacilitiesByCountry($limit){
        try {
            $fac = Facilities::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getFacilitiesByLocation($type){
        try {
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
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalFacilitiesBySides(){
        try {
            $fac = Facilities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getFacilitiesType(){
        try {
            $fac = Facilities::selectRaw('type')
                ->groupBy('type')
                ->get();
        
            return response()->json([
                'message' => count($fac)." Data retrived", 
                'status' => 'success',
                'data' => $fac
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

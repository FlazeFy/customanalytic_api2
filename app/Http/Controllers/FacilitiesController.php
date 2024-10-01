<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Facilities;
use App\Helpers\Generator;

class FacilitiesController extends Controller
{
    /**
     * @OA\GET(
     *     path="/api/facilities/summary",
     *     summary="Show facilities summary",
     *     tags={"Facility"},
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
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
                    (SELECT CAST(AVG(total) as UNSIGNED) 
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
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

    /**
     * @OA\GET(
     *     path="/api/facilities/total/bytype/{limit}",
     *     summary="Total facilities by type",
     *     tags={"Facility"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of facility per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalFacilitiesByType($limit){
        try {
            $fac = Facilities::selectRaw('type as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

    /**
     * @OA\GET(
     *     path="/api/facilities/total/bycountry/{limit}",
     *     summary="Total facilities by country",
     *     tags={"Facility"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of facility per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalFacilitiesByCountry($limit){
        try {
            $fac = Facilities::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

    /**
     * @OA\GET(
     *     path="/api/facilities/total/bylocation/{type}",
     *     summary="Total facilities by location and type",
     *     tags={"Facility"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Airfield"
     *         ),
     *         description="Filter by field type. Can be facility type or 'NULL'"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
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
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

    /**
     * @OA\GET(
     *     path="/api/facilities/total/bysides",
     *     summary="Total facilities by side",
     *     tags={"Facility"},
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalFacilitiesBySides(){
        try {
            $fac = Facilities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

    /**
     * @OA\GET(
     *     path="/api/facilities/type",
     *     summary="Show all facility type",
     *     tags={"Facility"},
     *     @OA\Response(
     *         response=200,
     *         description="facilities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getFacilitiesType(){
        try {
            $fac = Facilities::selectRaw('type')
                ->groupBy('type')
                ->get();
        
            return response()->json([
                //'message' => count($fac)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
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

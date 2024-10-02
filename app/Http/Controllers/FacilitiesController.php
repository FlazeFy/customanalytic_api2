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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getFacilitiesSummary(){
        try {
            $res = Facilities::selectRaw("type as most_built, count(*) as 'total', 
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
            
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getTotalFacilitiesByType($limit){
        try {
            $res = Facilities::selectRaw('type as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getTotalFacilitiesByCountry($limit){
        try {
            $res = Facilities::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getFacilitiesByLocation($type){
        try {
            if($type != "NULL"){
                $res = Facilities::selectRaw('name, type, location, country, coordinate')
                    ->where('coordinate', '!=', '')
                    ->where('type', $type)
                    ->get();
            } else {
                $res = Facilities::selectRaw('name, type, location, country, coordinate')
                    ->where('coordinate', '!=', '')
                    ->get();
            }
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getTotalFacilitiesBySides(){
        try {
            $res = Facilities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
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
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function getFacilitiesType(){
        try {
            $res = Facilities::selectRaw('type')
                ->groupBy('type')
                ->get();

            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'facilities', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'facilities', null),
                    'status' => 'failed'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

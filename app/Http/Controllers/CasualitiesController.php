<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Casualities;
use App\Helpers\Generator;

class CasualitiesController extends Controller
{
   /**
     * @OA\GET(
     *     path="/api/casualities/limit/{page_limit}/order/{orderby}/{ordertype}",
     *     summary="Show all casualities with ordering",
     *     tags={"Casualities"},
     *     @OA\Response(
     *         response=200,
     *         description="casualities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllCasualities($page_limit, $orderby, $ordertype){
        try {
            $cst = Casualities::select('id', 'country', 'continent', 'total_population', 'military_death', 'civilian_death', 'total_death', 'death_per_pop', 'avg_death_per_pop', 'military_wounded')
                ->orderBy($orderby, $ordertype)
                ->paginate($page_limit);
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'casualities', null),
                'status' => 'success',
                'data' => $cst
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
     *     path="/api/casualities/totaldeath/bycountry/{order}/limit/{page_limit}",
     *     summary="Total death by country",
     *     tags={"Casualities"},
     *     @OA\Response(
     *         response=200,
     *         description="casualities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalDeathByCountry($order, $page_limit){
        try {
            if($order != "NULL"){
                $cst = Casualities::selectRaw('country as context, total_population, military_death, civilian_death, military_death + civilian_death as total')
                    ->whereRaw('military_death+civilian_death != 0')
                    ->orderBy("total", $order)
                    ->paginate($page_limit);
            } else {
                $cst = Casualities::selectRaw('country as context, total_population, military_death, civilian_death, military_death + civilian_death as total')
                    ->whereRaw('military_death+civilian_death != 0')
                    ->paginate($page_limit);
            }   
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'casualities', null),
                'status' => 'success',
                'data' => $cst
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
     *     path="/api/casualities/summary",
     *     summary="Show casualities summary",
     *     tags={"Casualities"},
     *     @OA\Response(
     *         response=200,
     *         description="casualities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getCasualitiesSummary(){
        try {
            $cst = Casualities::selectRaw("(SELECT cast(avg(military_death + civilian_death) as decimal(10,0)) from casualities) as average_death, 
                    max(military_death + civilian_death) as 'highest_death', 
                    country as highest_death_country, 
                    cast((max(military_death + civilian_death) / (SELECT sum(military_death + civilian_death) from casualities) * 100) as decimal(10,2)) as highest_death_country_percent,
                    (SELECT sum(military_death + civilian_death) from casualities) as total_death_all")
                ->whereRaw('military_death + civilian_death = ( SELECT MAX(military_death + civilian_death) FROM casualities)')
                ->groupBy('military_death', 'civilian_death', 'country')
                ->get();

            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'casualities', null),
                'status' => 'success',
                'data' => $cst
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
     *     path="/api/casualities/totaldeath/bysides/{view}",
     *     summary="Total death by sides",
     *     tags={"Casualities"},
     *     @OA\Response(
     *         response=200,
     *         description="casualities found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalDeathBySides($view){
        try {
            if($view == "military" || $view == "civilian"){
                $cst = Casualities::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                        OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                        OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, sum('.$view.'_death) as total')
                    ->groupByRaw("1")
                    ->get();
            
                return response()->json([
                    //'message' => count($cst)." Data retrived", 
                    'message' => Generator::getMessageTemplate("api_read", 'casualities', null),
                    'status' => 'success',
                    'data' => $cst
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => "view not found", 
                    "status" => 'success',
                    "data" => null
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

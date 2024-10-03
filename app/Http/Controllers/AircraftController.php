<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Aircraft;
use App\Models\Histories;

class AircraftController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/aircraft",
     *     summary="Add aircraft",
     *     tags={"Aircraft"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="New aircraft ... has been created"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Data is already exist"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}"
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
                $msg = Generator::getMessageTemplate("api_create", "aircraft", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "aircraft",
                    'body' => $msg
                ];
                $data->merge($obj);

                $validatorHistory = Validation::getValidateHistory($data);

                if ($validatorHistory->fails()) {
                    $errors = $validatorHistory->messages();

                    return response()->json([
                        'status' => 'error',
                        'result' => $errors,
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                } else {
                    $check = Aircraft::selectRaw('1')->where('name', $request->name)->first();
                    $user_id = $request->user()->id;

                    if($check == null){
                        $uuid = Generator::getUUID();
                        Aircraft::create([
                            'id' => $uuid,
                            'name' => $request->name,
                            'primary_role' => $request->primary_role,
                            'manufacturer' => $request->manufacturer,
                            'country' => $request->country,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'updated_at' => null,
                            'updated_by' => null,
                        ]);

                        Histories::create([
                            'id' => Generator::getUUID(),
                            'history_type' => $data->type, 
                            'body' => $data->body,
                            'created_at' => date("Y-m-d H:i:s"),
                            'created_by' => $user_id
                        ]);
                
                        return response()->json([
                            "message" => $msg, 
                            "status" => 'success'
                        ], Response::HTTP_OK);
                    }else{
                        return response()->json([
                            "message" => "Data is already exist", 
                            "status" => 'failed'
                        ], Response::HTTP_CONFLICT);
                    }
                }
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
     *     path="/api/aircraft/limit/{limit}/order/{order}/find/{search}",
     *     summary="Show all aircraft with pagination, ordering, and search",
     *     tags={"Aircraft"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of aircraft per page"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="asc"
     *         ),
     *         description="Order by field name"
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Ar"
     *         ),
     *         description="Search term based on the field name or manufacturer"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="aircraft not found"
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
    public function getAllAircraft($limit, $order, $search){
        try {
            $search = trim($search);

            $air = Aircraft::select('id', 'name', 'primary_role', 'manufacturer', 'country')
                ->orderBy('name', $order);

            // Filtering
            if($search != "" && $search != "%20"){
                $air = $air->where('name', 'LIKE', '%' . $search . '%')
                    ->orwhere('manufacturer', 'LIKE', '%' . $search . '%');
            }

            $air = $air->paginate($limit);

            if($air->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                    "status" => 'success',
                    "data" => $air
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'aircraft', null),
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
     *     path="/api/aircraft",
     *     summary="Show all aircraft module or combined API (all data & stats)",
     *     tags={"Aircraft"},
     *     @OA\Parameter(
     *         name="limit_data_all",
     *         in="query",
     *         description="Limit the number of aircraft to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order_data_all",
     *         in="query",
     *         description="Order the aircraft by ascending or descending",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             default="asc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_data_all",
     *         in="query",
     *         description="Search term for filtering aircraft",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="%20"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit_stats_by_role",
     *         in="query",
     *         description="Limit the number of role to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit_stats_by_country",
     *         in="query",
     *         description="Limit the number of country to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit_stats_by_manufacturer",
     *         in="query",
     *         description="Limit the number of manufacturer to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="aircraft module found"
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
    public function getAircraftModule(Request $request){
        try {
            $data_all = json_decode(
                $this->getAllAircraft(
                    $request->limit_data_all ?? 20,
                    $request->order_data_all ?? 'asc',
                    $request->search_data_all ?? '%20'
                )->getContent(), true)['data'];
    
            $total_by_role = json_decode(
                $this->getTotalAircraftByRole(
                    $request->limit_stats_by_role ?? 7
                )->getContent(), true)['data'];
    
            $total_by_country = json_decode(
                $this->getTotalAircraftByCountry(
                    $request->limit_stats_by_country ?? 7
                )->getContent(), true)['data'];
    
            $total_by_sides = json_decode(
                $this->getTotalAircraftBySides()->getContent(), true)['data'];
    
            $total_by_manufacturer = json_decode(
                $this->getTotalAircraftByManufacturer(
                    $request->limit_stats_by_manufacturer ?? 7
                )->getContent(), true)['data'];

            $summary = json_decode(
                $this->getAircraftSummary()->getContent(), true)['data'];

            return response()->json([
                "message" => Generator::getMessageTemplate("api_read", 'aircraft module', null),
                "status" => 'success',
                "data_all" => $data_all,
                "stats" => [
                    "total_by_role" => $total_by_role,
                    "total_by_country" => $total_by_country,
                    "total_by_sides" => $total_by_sides,
                    "total_by_manufacturer" => $total_by_manufacturer,
                ],
                "summary" => $summary
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/aircraft/summary",
     *     summary="Show aircraft summary",
     *     tags={"Aircraft"},
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
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
                    (SELECT CAST(AVG(total) as UNSIGNED) 
                    FROM (
                        SELECT COUNT(*) as total
                        FROM aircraft
                        WHERE primary_role = 'Fighter'
                        GROUP BY country
                    ) q) AS average_by_country
                    ")
                ->groupBy('primary_role')
                ->orderBy('total', 'DESC')
                ->first();

            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/aircraft/total/byrole/{limit}",
     *     summary="Total aircraft by role",
     *     tags={"Aircraft"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of aircraft per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
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
    public function getTotalAircraftByRole($limit){
        try {
            $air = Aircraft::selectRaw('primary_role as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\GET(
     *     path="/api/aircraft/total/bymanufacturer/{limit}",
     *     summary="Total aircraft by manufacturer",
     *     tags={"Aircraft"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of aircraft per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
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
    public function getTotalAircraftByManufacturer($limit){
        try {
            $air = Aircraft::selectRaw('manufacturer as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\GET(
     *     path="/api/aircraft/total/bysides",
     *     summary="Total aircraft by sides",
     *     tags={"Aircraft"},
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
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
    public function getTotalAircraftBySides(){
        try {
            $air = Aircraft::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\GET(
     *     path="/api/aircraft/total/bycountry/{limit}",
     *     summary="Total aircraft by country",
     *     tags={"Aircraft"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of aircraft per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="aircraft found"
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
    public function getTotalAircraftByCountry($limit){
        try {
            $air = Aircraft::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'aircraft', null),
                'status' => 'success',
                'data' => $air
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/aircraft/{id}",
     *     summary="Update aircraft by id",
     *     tags={"Aircraft"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="aircraft ... has been updated"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}"
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
                $msg = Generator::getMessageTemplate("api_update", "aircraft", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "aircraft",
                    'body' => $msg
                ];
                $data->merge($obj);

                $validatorHistory = Validation::getValidateHistory($data);

                if ($validatorHistory->fails()) {
                    $errors = $validatorHistory->messages();

                    return response()->json([
                        'status' => 'failed',
                        'result' => $errors,
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                } else {
                    $user_id = $request->user()->id;
                    Aircraft::where('id', $id)->update([
                        'name' => $request->name,
                        'primary_role' => $request->primary_role,
                        'manufacturer' => $request->manufacturer,
                        'country' => $request->country,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $user_id,
                    ]);

                    Histories::create([
                        'id' => Generator::getUUID(),
                        'history_type' => $data->type, 
                        'body' => $data->body,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_id
                    ]);
            
                    return response()->json([
                        "message" => $msg, 
                        "status" => 'success'
                    ], Response::HTTP_OK);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/aircraft/{id}",
     *     summary="Delete aircraft by id",
     *     tags={"Aircraft"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="aircraft ... has been deleted"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="protected route need to include sign in token as authorization bearer",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="failed"),
     *             @OA\Property(property="message", type="string", example="you need to include the authorization token from login")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}"
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
    public function deleteAircraftById($id){
        try {
            $air = Aircraft::selectRaw("concat (name, ' - ', primary_role) as final_name")
                ->where('id', $id)
                ->first();

            $msg = Generator::getMessageTemplate("api_delete", "aircraft", $air->final_name);
            $data = new Request();
            $obj = [
                'type' => "aircraft",
                'body' => $msg
            ];
            $data->merge($obj);

            $validatorHistory = Validation::getValidateHistory($data);

            if ($validatorHistory->fails()) {
                $errors = $validatorHistory->messages();

                return response()->json([
                    'status' => 'failed',
                    'result' => $errors,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {     
                $user_id = $request->user()->id;
                Aircraft::destroy($id);

                Histories::create([
                    'id' => Generator::getUUID(),
                    'history_type' => $data->type, 
                    'body' => $data->body,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => $user_id
                ]);

                return response()->json([
                    'message' => $msg,
                    "status" => 'success'
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Vehicles;
use App\Models\Histories;

class VehiclesController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/vehicles",
     *     summary="Add vehicle",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="New vehicle ... has been created"
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
    public function createVehicles(Request $request)
    {
        try {
            $validator = Validation::getValidateVehicle($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $msg = Generator::getMessageTemplate("api_create", "vehicle", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "vehicle",
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
                    $check = Vehicles::selectRaw('1')->where('name', $request->name)->first();
                    
                    if($check == null){
                        $uuid = Generator::getUUID();
                        $user_id = $request->user()->id;

                        Vehicles::create([
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
                            "status" => 'error'
                        ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
     *     path="/api/vehicles/limit/{limit}/order/{order}/find/{search}",
     *     summary="Show all vehicles with pagination, ordering, and search",
     *     tags={"Vehicle"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of vehicle per page"
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
     *             example="SdKfz"
     *         ),
     *         description="Search term based on the field name or manufacturer"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Vehicle found"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="string", example="103"),
     *                          @OA\Property(property="name", type="string", example="SdKfz 10"),
     *                          @OA\Property(property="primary_role", type="string", example="Transport"),
     *                          @OA\Property(property="manufacturer", type="string", example="Deutsche Maschinenfabrik AG"),
     *                          @OA\Property(property="country", type="string", example="Germany")
     *                      )
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="vehicle not found"),
     *             @OA\Property(property="status", type="string", example="failed")
     *         )
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
    public function getAllVehicles($limit, $order, $search){
        try {
            $search = trim($search);

            $vhc = Vehicles::select('id', 'name', 'primary_role', 'manufacturer', 'country')
                ->orderBy('name', $order);

            // Filtering
            if($search != "" && $search != "%20"){
                $vhc = $vhc->where('name', 'LIKE', '%' . $search . '%')
                    ->orwhere('manufacturer', 'LIKE', '%' . $search . '%');
            }

            $vhc = $vhc->paginate($limit);
        
            if($vhc->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'vehicle', null),
                    'status' => 'success',
                    'data' => $vhc
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'vehicle', null),
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
     *     path="/api/vehicles/summary",
     *     summary="Show vehicle summary",
     *     tags={"Vehicle"},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="vehicle not found"),
     *             @OA\Property(property="status", type="string", example="failed")
     *         )
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
    public function getVehiclesSummary(){
        try {
            $res = Vehicles::selectRaw("primary_role as most_produced, count(*) as 'total', 
                    (SELECT GROUP_CONCAT(' ',country)
                    FROM (
                        SELECT country 
                        FROM vehicles 
                        WHERE primary_role = 'Light Tank'
                        GROUP BY country
                        ORDER BY count(id) DESC LIMIT 3
                    ) q) most_produced_by_country, 
                    (SELECT CAST(AVG(total) as UNSIGNED) 
                    FROM (
                        SELECT COUNT(*) as total
                        FROM vehicles
                        WHERE primary_role = 'Light Tank'
                        GROUP BY country
                    ) q) AS average_by_country
                    ")
                ->groupBy('primary_role')
                ->orderBy('total', 'DESC')
                ->first();

            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'vehicle', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'vehicle', null),
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
     *     path="/api/vehicles/total/byrole/{limit}",
     *     summary="Total vehicle by role",
     *     tags={"Vehicle"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of vehicle per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Vehicle found"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="context", type="string", example="Armored Tank"),
     *                      @OA\Property(property="total", type="number", example=80)
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="vehicle not found"),
     *             @OA\Property(property="status", type="string", example="failed")
     *         )
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
    public function getTotalVehiclesByRole($limit){
        try {
            $res = Vehicles::selectRaw('primary_role as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'vehicle', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'vehicle', null),
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
     *     path="/api/vehicles/total/bycountry/{limit}",
     *     summary="Total vehicle by country",
     *     tags={"Vehicle"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of vehicle per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Vehicle found"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="context", type="string", example="United States"),
     *                      @OA\Property(property="total", type="number", example=80)
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="vehicle not found"),
     *             @OA\Property(property="status", type="string", example="failed")
     *         )
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
    public function getTotalVehiclesByCountry($limit){
        try {
            $res = Vehicles::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'vehicle', null),
                    'status' => 'success',
                    'data' =>$res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'vehicle', null),
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
     *     path="/api/vehicles/total/bysides",
     *     summary="Total vehicle by sides",
     *     tags={"Vehicle"},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Vehicle found"),
     *             @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="context", type="string", example="axis"),
     *                      @OA\Property(property="total", type="number", example=80)
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="vehicle failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="vehicle not found"),
     *             @OA\Property(property="status", type="string", example="failed")
     *         )
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
    public function getTotalVehiclesBySides(){
        try {
            $res = Vehicles::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'vehicle', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'vehicle', null),
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
     *     path="/api/vehicles",
     *     summary="Show all vehicles module or combined API (all data & stats)",
     *     tags={"Vehicle"},
     *     @OA\Parameter(
     *         name="limit_data_all",
     *         in="query",
     *         description="Limit the number of vehicles to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order_data_all",
     *         in="query",
     *         description="Order the vehicles by ascending or descending",
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
     *         description="Search term for filtering vehicles",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="%20"
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
     *         name="limit_stats_by_role",
     *         in="query",
     *         description="Limit the number of role to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="vehicle module found"
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
    public function getVehiclesModule(Request $request){
        try {
            $data_all = json_decode(
                $this->getAllVehicles(
                    $request->limit_data_all ?? 20,
                    $request->order_data_all ?? 'asc',
                    $request->search_data_all ?? '%20'
                )->getContent(), true)['data'];
    
            $total_by_role = json_decode(
                $this->getTotalVehiclesByRole(
                    $request->limit_stats_by_role ?? 7
                )->getContent(), true)['data'];
    
            $total_by_country = json_decode(
                $this->getTotalVehiclesByCountry(
                    $request->limit_stats_by_country ?? 7
                )->getContent(), true)['data'];

            $total_by_sides = json_decode(
                $this->getTotalVehiclesBySides()->getContent(), true)['data'];

            $summary = json_decode(
                $this->getVehiclesSummary()->getContent(), true)['data'];

            return response()->json([
                "message" => Generator::getMessageTemplate("api_read", 'vehicle module', null),
                "status" => 'success',
                "data_all" => $data_all,
                "stats" => [
                    "total_by_role" => $total_by_role,
                    "total_by_country" => $total_by_country,
                    "total_by_sides" => $total_by_sides,
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
     * @OA\PUT(
     *     path="/api/vehicles/{id}",
     *     summary="Update vehicle by id",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle ... has been updated"
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
    public function updateVehicleById(Request $request, $id){
        try {
            $validator = Validation::getValidateVehicle($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'message' => $errors, 
                    'status' => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $msg = Generator::getMessageTemplate("api_update", "vehicle", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "vehicle",
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

                    Vehicles::where('id', $id)->update([
                        'name' => $request->name,
                        'primary_role' => $request->primary_role,
                        'manufacturer' => $request->manufacturer,
                        'country' => $request->country,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $user_id
                    ]);

                    Histories::create([
                        'id' => Generator::getUUID(),
                        'history_type' => $data->type, 
                        'body' => $data->body,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_id
                    ]);
            
                    return response()->json([
                        'message' => $msg, 
                        'status' => 'success'
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
     *     path="/api/vehicles/{id}",
     *     summary="Delete vehicle by id",
     *     tags={"Vehicle"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="vehicle ... has been updated"
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
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something wrong. please contact admin")
     *         )
     *     ),
     * )
     */
    public function deleteVehiclesById($id){
        try {
            $vhc = Vehicles::selectRaw("concat (name, ' - ', primary_role) as final_name")
                ->where('id', $id)
                ->first();
            
            $msg = Generator::getMessageTemplate("api_delete", "vehicle", $vhc->final_name);
            $data = new Request();
            $obj = [
                'type' => "vehicle",
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
                
                Vehicles::destroy($id);

                Histories::create([
                    'id' => Generator::getUUID(),
                    'history_type' => $data->type, 
                    'body' => $data->body,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => $user_id
                ]);

                return response()->json([
                    'message' => $msg, 
                    'status' => 'success'
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

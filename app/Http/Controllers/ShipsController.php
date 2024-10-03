<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Ships;
use App\Models\Histories;

class ShipsController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/ships",
     *     summary="Add ship",
     *     tags={"Ships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="New ships ... has been created"
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
                    $msg = Generator::getMessageTemplate("api_create", "ships", $request->name);
                    $data = new Request();
                    $obj = [
                        'type' => "ships",
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
                        $uuid = Generator::getUUID();
                        $user_id = $request->user()->id;

                        Ships::create([
                            'id' => $uuid,
                            'name' => $request->name,
                            'class' => $request->class,
                            'country' => $request->country,
                            'launch_year' => $request->launch_year,
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
                    }
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
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/ships/limit/{limit}/order/{order}/find/{search}",
     *     summary="Show all ships with pagination, ordering, and search",
     *     tags={"Ships"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of ships per page"
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
     *             example="Peter"
     *         ),
     *         description="Search term based on the field name"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ship found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Ship found"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="string", example="142"),
     *                          @OA\Property(property="name", type="string", example="Carl Peters"),
     *                          @OA\Property(property="class", type="string", example="Carl Peters-class Tender"),
     *                          @OA\Property(property="launch_year", type="string", example="1939"),
     *                          @OA\Property(property="country", type="string", example="Germany")
     *                      )
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ship failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ship not found"),
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
    public function getAllShips($limit, $order, $search){
        try {
            $search = trim($search);

            $shp = Ships::select('id', 'name', 'class', 'country', 'launch_year')
                ->orderBy('name', $order);

            // Filtering
            if($search != "" && $search != "%20"){
                $shp = $shp->where('name', 'LIKE', '%' . $search . '%');
            }

            $shp = $shp->paginate($limit);
        
            if($shp->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                    'status' => 'success',
                    'data' => $shp
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'ship', null),
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
     *     path="/api/ships/summary",
     *     summary="Show ship summary",
     *     tags={"Ships"},
     *     @OA\Response(
     *         response=200,
     *         description="ship found"
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
                    (SELECT CAST(AVG(total) as UNSIGNED) 
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
                ->first();

            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                "status"=> 'success',
                "data"=> $shp
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
     *     path="/api/ships/total/by/{limit}",
     *     summary="Total ship by class",
     *     tags={"Ships"},
     *     @OA\Response(
     *         response=200,
     *         description="ship found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ship failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ship not found"),
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
    public function getTotalShipsByClass($limit){
        try {
            $res = Ships::selectRaw('class as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'ship', null), 
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'ship', null),
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
     *     path="/api/ships/total/bycountry/{limit}",
     *     summary="Total ship by country",
     *     tags={"Ships"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of ships per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ship found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ship failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ship not found"),
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
    public function getTotalShipsByCountry($limit){
        try {
            $res = Ships::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();

            if($res){
                return response()->json([ 
                    'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'ship', null),
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
     *     path="/api/ships/total/bysides",
     *     summary="Total ship by sides",
     *     tags={"Ships"},
     *     @OA\Response(
     *         response=200,
     *         description="ship found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ship failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ship not found"),
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
    public function getTotalShipsBySides(){
        try {
            $res = Ships::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();

            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'ship', null),
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
     *     path="/api/ships/total/bylaunchyear",
     *     summary="Total ship by launch year",
     *     tags={"Ships"},
     *     @OA\Response(
     *         response=200,
     *         description="ship found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ship failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="ship not found"),
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
    public function getTotalShipsByLaunchYear(){
        try {
            $res = Ships::selectRaw('launch_year as context, count(*) as total')
                ->where('launch_year', '!=', 0000)
                ->whereRaw('char_length(launch_year) = 5')
                ->groupBy('launch_year')
                ->orderBy('total', 'DESC')
                ->get();
        
            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'ship', null),
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'ship', null),
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
     *     path="/api/ships",
     *     summary="Show all ship module or combined API (all data & stats)",
     *     tags={"Ships"},
     *     @OA\Parameter(
     *         name="limit_data_all",
     *         in="query",
     *         description="Limit the number of ships to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order_data_all",
     *         in="query",
     *         description="Order the ships by ascending or descending",
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
     *         description="Search term for filtering ships",
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
     *         name="limit_stats_by_class",
     *         in="query",
     *         description="Limit the number of class to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ship module found"
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
    public function getShipsModule(Request $request){
        try {
            $data_all = json_decode(
                $this->getAllShips(
                    $request->limit_data_all ?? 20,
                    $request->order_data_all ?? 'asc',
                    $request->search_data_all ?? '%20'
                )->getContent(), true)['data'];
    
            $total_by_class = json_decode(
                $this->getTotalShipsByClass(
                    $request->limit_stats_by_class ?? 7
                )->getContent(), true)['data'];
    
            $total_by_country = json_decode(
                $this->getTotalShipsByCountry(
                    $request->limit_stats_by_country ?? 7
                )->getContent(), true)['data'];
    
            $total_by_launch_year = json_decode(
                $this->getTotalShipsByLaunchYear()->getContent(), true)['data'];

            $total_by_sides = json_decode(
                $this->getTotalShipsBySides()->getContent(), true)['data'];

            $summary = json_decode(
                $this->getShipsSummary()->getContent(), true)['data'];

            return response()->json([
                "message" => Generator::getMessageTemplate("api_read", 'ship module', null),
                "status" => 'success',
                "data_all" => $data_all,
                "stats" => [
                    "total_by_class" => $total_by_class,
                    "total_by_country" => $total_by_country,
                    "total_by_sides" => $total_by_sides,
                    "total_by_launch_year" => $total_by_launch_year,
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
     *     path="/api/ships/{id}",
     *     summary="Update ships by id",
     *     tags={"Ships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="ships ... has been updated"
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
                $msg = Generator::getMessageTemplate("api_update", "ships", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "ships",
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

                    Ships::where('id', $id)->update([
                        'name' => $request->name,
                        'class' => $request->class,
                        'country' => $request->country,
                        'launch_year' => $request->launch_year,
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
     *     path="/api/ships/{id}",
     *     summary="Delete ship by id",
     *     tags={"Ships"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="ships ... has been deleted"
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
    public function deleteShipById($id){
        try {
            $shp = Ships::selectRaw("concat (name, ' - ', class) as final_name")
                ->where('id', $id)
                ->first();
            
            $msg = Generator::getMessageTemplate("api_delete", "ships", $shp->final_name);
            $data = new Request();
            $obj = [
                'type' => "ships",
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

                Ships::destroy($id);
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

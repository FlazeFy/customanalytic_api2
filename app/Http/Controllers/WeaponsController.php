<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Weapons;
use App\Models\Histories;

class WeaponsController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/weapons",
     *     summary="Add weapon",
     *     tags={"Weapon"},
     *     @OA\Response(
     *         response=200,
     *         description="New weapon ... has been created"
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
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function createWeapon(Request $request)
    {
        try {
            $validator = Validation::getValidateWeapon($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Weapons::selectRaw('1')->where('name', $request->name)->first();
                
                if($check == null){
                    $msg = Generator::getMessageTemplate("api_create", "weapon", $request->name);
                    $data = new Request();
                    $obj = [
                        'type' => "weapon",
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

                        Weapons::create([
                            'id' => $uuid,
                            'name' => $request->name,
                            'type' => $request->type,
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
                            'created_by' => '1' // for now
                        ]);
                
                        return response()->json([
                            'message' => $msg, 
                            'status' => 'success'
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/weapons/limit/{limit}/order/{order}/find/{search}",
     *     summary="Show all weapons with pagination, ordering, and search",
     *     tags={"Weapon"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of weapon per page"
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
     *             example="75 mm"
     *         ),
     *         description="Search term based on the field name"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="weapon found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllWeapons($limit, $order, $search){
        try {
            $search = trim($search);

            $wpn = Weapons::select('id', 'name', 'type', 'country')
                ->orderBy('name', $order);
            
            // Filtering
            if($search != "" && $search != "%20"){
                $wpn = $wpn->where('name', 'LIKE', '%' . $search . '%');
            }

            $wpn = $wpn->paginate($limit);
        
            return response()->json([
                //'message' => count($wpn)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'weapon', null),
                'status' => 'success',
                'data' => $wpn
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
     *     path="/api/weapons/summary",
     *     summary="Show weapon summary",
     *     tags={"Weapon"},
     *     @OA\Response(
     *         response=200,
     *         description="weapon found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getWeaponsSummary(){
        try {
            $wpn = Weapons::selectRaw("type as most_produced, count(*) as 'total', 
                    (SELECT GROUP_CONCAT(' ',country)
                    FROM (
                        SELECT country 
                        FROM weapons WHERE type = 'Field Gun '
                        GROUP BY country
                        ORDER BY count(id) DESC LIMIT 3
                    ) q) most_produced_by_country, 
                    (SELECT CAST(AVG(total) as int) 
                    FROM (
                        SELECT COUNT(*) as total
                        FROM weapons
                        WHERE type = 'Field Gun '
                        GROUP BY country
                    ) q) AS average_by_country
                    ")
                ->groupBy('type')
                ->orderBy('total', 'DESC')
                ->limit(1)
                ->get();

            return response()->json([
                //'message' => count($wpn)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'weapon', null),
                'status' => 'success',
                'data' => $wpn
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
     *     path="/api/weapons/total/bytype/{limit}",
     *     summary="Total weapon by type",
     *     tags={"Weapon"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of weapon per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="weapon found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalWeaponsByType($limit){
        try {
            $wpn = Weapons::selectRaw('type as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                //'message' => count($wpn)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'weapon', null),
                'status' => 'success',
                'data' => $wpn
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
     *     path="/api/weapons/total/bycountry/{limit}",
     *     summary="Total weapon by country",
     *     tags={"Weapon"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of weapon per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="weapon found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalWeaponsByCountry($limit){
        try {
            $wpn = Weapons::selectRaw('country as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                //'message' => count($wpn)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'weapon', null),
                'status' => 'success',
                'data' => $wpn
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
     *     path="/api/weapons/total/bysides",
     *     summary="Total weapon by sides",
     *     tags={"Weapon"},
     *     @OA\Response(
     *         response=200,
     *         description="weapon found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalWeaponsBySides(){
        try {
            $wpn = Weapons::selectRaw('(CASE WHEN country = "Germany" OR country = "Italy" OR country = "Japan" OR country = "Thailand" 
                OR country = "Austria" OR country = "Hungary" OR country = "Romania" OR country = "Bulgaria" 
                OR country = "Albania" OR country = "Finland" THEN "Axis" ELSE "Allies" END) AS context, COUNT(*) as total')
                ->groupByRaw('1')
                ->get();
        
            return response()->json([
                //'message' => count($wpn)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'weapon', null),
                'status' => 'success',
                'data' => $wpn
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getWeaponsModule(Request $request){
        try {
            $data_all = json_decode(
                $this->getAllWeapons(
                    $request->limit_data_all ?? 20,
                    $request->order_data_all ?? 'asc',
                    $request->search_data_all ?? '%20'
                )->getContent(), true)['data'];
    
            $total_by_type = json_decode(
                $this->getTotalWeaponsByType(
                    $request->limit_stats_by_type ?? 7
                )->getContent(), true)['data'];
    
            $total_by_country = json_decode(
                $this->getTotalWeaponsByCountry(
                    $request->limit_stats_by_country ?? 7
                )->getContent(), true)['data'];

            $total_by_sides = json_decode(
                $this->getTotalWeaponsBySides()->getContent(), true)['data'];

            return response()->json([
                "message" => Generator::getMessageTemplate("api_read", 'weapon module', null),
                "status" => 'success',
                "data_all" => $data_all,
                "stats" => [
                    "total_by_type" => $total_by_type,
                    "total_by_country" => $total_by_country,
                    "total_by_sides" => $total_by_sides,
                ]
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/weapons/{id}",
     *     summary="Update weapon by id",
     *     tags={"Weapon"},
     *     @OA\Response(
     *         response=200,
     *         description="weapon ... has been updated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function updateWeaponById(Request $request, $id){
        try {
            $validator = Validation::getValidateWeapon($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $msg = Generator::getMessageTemplate("api_update", "weapon", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "weapon",
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
                    Weapons::where('id', $id)->update([
                        'name' => $request->name,
                        'type' => $request->type,
                        'country' => $request->country,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => null,
                    ]);

                    Histories::create([
                        'id' => Generator::getUUID(),
                        'history_type' => $data->type, 
                        'body' => $data->body,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => '1' // for now
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     * @OA\DELETE(
     *     path="/api/weapons/{id}",
     *     summary="Delete weapon by id",
     *     tags={"Weapon"},
     *     @OA\Response(
     *         response=200,
     *         description="weapon ... has been updated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function deleteWeaponById($id){
        try {
            $wpn = Weapons::selectRaw("concat (name, ' - ', type) as final_name")
                ->where('id', $id)
                ->first();

            $msg = Generator::getMessageTemplate("api_delete", "weapon", $wpn->final_name);
            $data = new Request();
            $obj = [
                'type' => "weapon",
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
                Weapons::destroy($id);

                Histories::create([
                    'id' => Generator::getUUID(),
                    'history_type' => $data->type, 
                    'body' => $data->body,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => '1' // for now
                ]);

                return response()->json([
                    'message' => $msg,
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
}

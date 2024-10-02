<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Events;
use App\Models\Histories;

class EventsController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/events",
     *     summary="Add event",
     *     tags={"Event"},
     *     @OA\Response(
     *         response=200,
     *         description="New event ... has been created"
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
    public function createEvent(Request $request)
    {
        try {
            $validator = Validation::getValidateEvent($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $check = Events::selectRaw('1')->where('event', $request->event)->first();
                
                if($check == null){
                    $msg = Generator::getMessageTemplate("api_create", "event", $request->name);
                    $data = new Request();
                    $obj = [
                        'type' => "event",
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

                        Events::create([
                            'id' => $uuid,
                            'event' => $request->event,
                            'date_start' => $request->date_start,
                            'date_end' => $request->date_end,
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
                            "message" => Generator::getMessageTemplate("api_create", "event", $request->event),
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
     *     path="/api/events/limit/{limit}/order/{order}",
     *     summary="Show all event with ordering",
     *     tags={"Event"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of events per page"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="asc"
     *         ),
     *         description="Order by field event"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="event found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="event not found"
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
    public function getAllEvents($limit, $order){
        try {
            $evt = Events::selectRaw('id, event, date_start, date_end, DATEDIFF(date_end, date_start) AS period')
                ->orderBy('event', $order)
                ->paginate($limit);
        
            if($evt->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'event', null),
                    "data" => $evt,
                    'status' => 'success'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'event', null),
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
     * @OA\PUT(
     *     path="/api/events/{id}",
     *     summary="Update event by id",
     *     tags={"Event"},
     *     @OA\Response(
     *         response=200,
     *         description="event ... has been updated"
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
    public function updateEventById(Request $request, $id){
        try {
            $validator = Validation::getValidateEvent($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $msg = Generator::getMessageTemplate("api_update", "event", $request->name);
                $data = new Request();
                $obj = [
                    'type' => "event",
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
                    Events::where('id', $id)->update([
                        'event' => $request->event,
                        'date_start' => $request->date_start,
                        'date_end' => $request->date_end,
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
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/events/{id}",
     *     summary="Delete event by id",
     *     tags={"Event"},
     *     @OA\Response(
     *         response=200,
     *         description="event ... has been updated"
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
    public function deleteEventById($id){
        try {
            $evt = Events::select('event')
                ->where('id', $id)
                ->first();

            $msg = Generator::getMessageTemplate("api_delete", "event",$evt->event);
            $data = new Request();
            $obj = [
                'type' => "event",
                'body' => $msg
            ];
            $data->merge($obj);

            $validatorHistory = Validation::getValidateHistory($data);

            if ($validatorHistory->fails()) {
                $errors = $validatorHistory->messages();


                Histories::create([
                    'id' => Generator::getUUID(),
                    'history_type' => $data->type, 
                    'body' => $data->body,
                    'created_at' => date("Y-m-d H:i:s"),
                    'created_by' => '1' // for now
                ]);
                
                return response()->json([
                    'status' => 'failed',
                    'result' => $errors,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {   
                Events::destroy($id);

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

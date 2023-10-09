<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Events;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
                    $uuid = Generator::getUUID();
                    Events::create([
                        'id' => $uuid,
                        'event' => $request->event,
                        'date_start' => $request->date_start,
                        'date_end' => $request->date_end,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => "1",
                        'updated_at' => null,
                        'updated_by' => null,
                    ]);
            
                    return response()->json([
                        "message" => Generator::getMessageTemplate("api_create", "event", $request->event),
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

    public function getAllEvents($page_limit, $order){
        try {
            $evt = Events::selectRaw('id, event, date_start, date_end, DATEDIFF(date_end, date_start) AS period')
                ->orderBy('event', $order)
                ->paginate($page_limit);
        
            return response()->json([
                //'message' => count($evt)." Data retrived", 
                'message' => Generator::getMessageTemplate("api_read", 'event', null),
                "data" => $evt
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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
                Events::where('id', $id)->update([
                    'event' => $request->event,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => null,
                ]);
        
                return response()->json([
                    "message" => Generator::getMessageTemplate("api_update", "airplane", $request->event),
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

    public function deleteEventById($id){
        try {
            $evt = Events::select('event')
                ->where('id', $id)
                ->first();
                
            Events::destroy($id);

            return response()->json([
                'message' => Generator::getMessageTemplate("api_delete", "event",$evt->event), 
                'status' => 'success'
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

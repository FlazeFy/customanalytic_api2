<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $validator = Validation::getValidateEvent($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            $check = Events::selectRaw('1')->where('event', $request->event)->first();
            
            if($check == null){
                $uuid = Generator::getUUID();
                Events::create([
                    'id' => $uuid,
                    'event' => $request->event,
                    'date' => $request->date,
                    'date_start' => $request->date_start,
                    'date_end' => $request->date_end,
                ]);
        
                return response()->json([
                    "msg" => "'".$request->event."' Data Created", 
                    "status" => 200
                ]);
            }else{
                return response()->json([
                    "msg" => "Data is already exist", 
                    "status" => 422
                ]);
            }
        }
    }

    public function getAllEvents($page_limit, $order){
        $evt = Events::select('id', 'event', 'date', 'date_start', 'date_end')
            ->orderBy('event', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($evt)." Data retrived", 
            "status"=>200,
            "data"=>$evt
        ]);
    }

    public function updateEventById(Request $request, $id){
        $validator = Validation::getValidateEvent($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            Events::where('id', $id)->update([
                'event' => $request->event,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
            ]);
    
            return response()->json([
                "msg" => "'".$request->event."' Data Updated", 
                "status" => 200
            ]);
        }
    }

    public function deleteEventById($id){
        $evt = Events::select('event')
            ->where('id', $id)
            ->first();
            Events::destroy($id);

        return response()->json([
            "msg"=> "'".$evt->event."' Data Destroyed", 
            "status"=>200
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Events;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAllEvents($page_limit, $order){
        $evt = Events::select('id', 'event', 'date')
            ->orderBy('event', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($evt)." Data retrived", 
            "status"=>200,
            "data"=>$evt
        ]);
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

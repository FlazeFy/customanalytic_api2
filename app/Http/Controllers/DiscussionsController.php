<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Discussions;
use App\Models\Histories;

class DiscussionsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDiscussion(Request $request)
    {
        try {
            $validator = Validation::GetValidateDiscussion($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Discussions::selectRaw('1')->where('stories_id', $request->stories_id)->first();
                
                if($check == null){
                    $msg = Generator::getMessageTemplate("api_create", "discussion", $request->stories_id);
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
                        Discussions::create([
                            'id' => $uuid,
                            'stories_id' => $request->stories_id,
                            'reply_id' => $request->reply_id,
                            'body' => $request->body,
                            'attachment' => $request->attachment,
                            'created_at' => $request->created_at,
                            'created_by' => $request->created_by,
                            'updated_at' => $request->update_at,
                            'updated_by' => $request->update_by
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

    public function getAllDiscussion($page_limit, $order)
    {
        try {
            $evt = Discussions::selectRaw('id, stories_id, reply_id, body, attachment, created_at, created_by, updated_at, updated_by')
                ->orderBy('event', $order)
                ->paginate($page_limit);
        
            return response()->json([
                'message' => count($evt)." Data retrived", 
                "data" => $evt
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

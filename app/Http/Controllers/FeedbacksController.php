<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Discussions;
use App\Models\Feedbacks;

class FeedbacksController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFeedback(Request $request)
    {
        try {
            $validator = Validation::getValidateFeedback($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $check = Feedbacks::selectRaw('1')->where('stories_id', $request->stories_id)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Feedbacks::create([
                        'id' => $uuid,
                        'stories_id' => $request->stories_id,
                        'body' => $request->body,
                        'rate' => $request->rate,
                        'created_at' => $request->created_at,
                        'created_by' => $request->created_by,
                    ]);
            
                    return response()->json([
                        'message' => Generator::getMessageTemplate("custom", "Feedback has sended", null), 
                        'status' => 'success'
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAllFeedback($page_limit, $order)
    {
        try {
            $evt = Feedbacks::selectRaw('id, stories_id, body, rate, created_at, created_by')
                ->orderBy('stories_id', $order)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

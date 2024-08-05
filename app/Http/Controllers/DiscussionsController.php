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
     * @OA\POST(
     *     path="/api/discussions",
     *     summary="Add discussion",
     *     tags={"Discussion"},
     *     @OA\Response(
     *         response=200,
     *         description="New discussion ... has been created"
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
                        $user_id = $request->user()->id;

                        Discussions::create([
                            'id' => $uuid,
                            'stories_id' => $request->stories_id,
                            'reply_id' => $request->reply_id,
                            'body' => $request->body,
                            'attachment' => $request->attachment,
                            'created_at' => $request->created_at,
                            'created_by' => $user_id,
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

    /**
     * @OA\GET(
     *     path="/api/discussions/limit/{limit}/order/{order}",
     *     summary="Show all discussions with ordering",
     *     tags={"Discussion"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of discussion per page"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="asc"
     *         ),
     *         description="Order by field created at"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="discussion found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllDiscussion($limit, $order)
    {
        try {
            $evt = Discussions::selectRaw('discussion.id as discussion_id, stories_id, reply_id, body, attachment, discussion.created_at, discussion.created_by, discussion.updated_at, discussion.updated_by')
                ->join('stories','stories.id','=','discussion.stories_id')
                ->orderBy('stories_id', 'DESC')
                ->orderBy('discussion.created_at', $order)
                ->paginate($limit);
        
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

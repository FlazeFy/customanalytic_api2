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
                $msg = Generator::getMessageTemplate("api_create", "discussion", null);
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
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_id,
                        'updated_at' => null,
                        'updated_by' => null
                    ]);

                    Histories::create([
                        'id' => Generator::getUUID(),
                        'history_type' => $data->type, 
                        'body' => $data->body,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_id,
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
     *         response=404,
     *         description="discussion not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllDiscussion($limit, $order, $id)
    {
        try {
            $evt = Discussions::selectRaw("discussions.id as discussion_id, stories_id, reply_id, body, attachment, discussions.created_at, discussions.updated_at,
                    CASE
                        WHEN admins.username IS NOT NULL THEN admins.username
                        WHEN users.username IS NOT NULL THEN users.username
                        ELSE NULL
                    END AS created_by,
                    CASE
                        WHEN admins.username IS NOT NULL THEN 'admin'
                        WHEN users.username IS NOT NULL THEN users.role
                        ELSE NULL
                    END AS role
                ")
                ->join('stories','stories.id','=','discussions.stories_id')
                ->leftjoin('users','users.id','=','discussions.created_by')
                ->leftjoin('admins','admins.id','=','discussions.created_by')
                ->where('stories.id',$id)
                ->orderBy('stories_id', 'DESC')
                ->orderBy('discussions.created_at', $order)
                ->paginate($limit);
        
            if($evt->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", "discussion", null), 
                    "data" => $evt,
                    'status' => 'success'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'discussion', null),
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
}

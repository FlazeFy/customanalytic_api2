<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Feedbacks;
use App\Models\Histories;

class FeedbacksController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/feedbacks",
     *     summary="Add feedback",
     *     tags={"Feedback"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="New feedback ... has been created"
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
                $msg = Generator::getMessageTemplate("custom", "Feedback has sended", null);
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

                    Feedbacks::create([
                        'id' => $uuid,
                        'stories_id' => $request->stories_id,
                        'body' => $request->body,
                        'rate' => $request->rate,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => $user_id,
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
     *     path="/api/feedbacks/limit/{limit}/order/{order}/{id}",
     *     summary="Show all feedbacks per stories with pagination, ordering, and search",
     *     tags={"Feedback"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of feedback per page"
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
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="10e62495-d508-b32f-2e8e-66fa84f43fe8"
     *         ),
     *         description="ID of the stories"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="feedback found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Feedback found"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="body", type="string", example="that content look so great!"),
     *                          @OA\Property(property="rate", type="number", example=5),
     *                          @OA\Property(property="created_at", type="string", example="2024-09-03 00:28:28"),
     *                          @OA\Property(property="created_by", type="string", example="testeradmin"),
     *                      )
     *                  )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="feedback failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="feedback not found"),
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
    public function getAllFeedback($limit, $order, $id)
    {
        try {
            $res = Feedbacks::selectRaw("body, rate, feedbacks.created_at,
                    CASE 
                        WHEN admins.username IS NOT NULL THEN admins.username
                        WHEN users.username IS NOT NULL THEN users.username
                        ELSE NULL
                    END AS created_by
                ")
                ->leftjoin('users','users.id','=','feedbacks.created_by')
                ->leftjoin('admins','admins.id','=','feedbacks.created_by')
                ->where('stories_id', $id)
                ->orderBy('created_at', $order)
                ->paginate($limit);
        
            if($res->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'feedback', null), 
                    "data" => $res,
                    'status' => 'success'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", 'feedback', null),
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
     *     path="/api/feedbacks/stats/{id}",
     *     summary="Show stories feedback stats by id",
     *     tags={"Feedback"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="10e62495-d508-b32f-2e8e-66fa84f43fe8"
     *         ),
     *         description="ID of the stories"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="feedback stats found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="feedback failed to fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="feedback stats not found"),
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
    public function getStoriesFeedbackStats($id)
    {
        try {
            $res = Feedbacks::selectRaw("rate, CAST(COUNT(rate) as UNSIGNED) as count")
                ->join('stories','stories.id','=','feedbacks.stories_id')
                ->where('stories_id', $id)
                ->groupby('rate')
                ->get();

            if(count($res) > 0){
                $res_summary = Feedbacks::selectRaw("CAST(SUM(1) as UNSIGNED) as total, CAST(AVG(rate) as UNSIGNED) as average")
                    ->where('stories_id', $id)
                    ->groupby('stories_id')
                    ->first();
            
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", 'feedback stats', null), 
                    'data' => [
                        'stats' => $res,
                        'summary' => $res_summary
                    ],
                    'status' => 'success'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => Generator::getMessageTemplate("api_read_empty", 'feedback stats', null),
                ], Response::HTTP_NOT_FOUND);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    //hard delete, admin only
    public function deleteFeedbackById($id)
    {
        $feedback = Feedbacks::select('stories_id')->where('id', $id)->first();
        Feedbacks::destroy($id);

        return response()->json([
            "msg" => Generator::getMessageTemplate("custom", "Feedback has deleted", null),
            "status" => 200
        ]);
    }
}

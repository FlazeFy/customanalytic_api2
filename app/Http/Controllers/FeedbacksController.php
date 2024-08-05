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
     *     @OA\Response(
     *         response=200,
     *         description="New feedback ... has been created"
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
                        'created_at' => $request->created_at,
                        'created_by' => $user_id,
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
     *         description="feedback found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllFeedback($limit, $order, $id)
    {
        try {
            $evt = Feedbacks::selectRaw('id, stories_id, body, rate, created_at, created_by')
                ->where('stories_id', $id)
                ->orderBy('created_at', $order)
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

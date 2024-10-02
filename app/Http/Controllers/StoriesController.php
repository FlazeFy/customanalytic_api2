<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;
use App\Helpers\Template;

use App\Models\Stories;
use App\Models\Histories;

class StoriesController extends Controller
{
    /**
     * @OA\GET(
     *     path="/api/stories/detail/{slug}",
     *     summary="Show stories by slug",
     *     tags={"Stories"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="lorem"
     *         ),
     *         description="Slug of the stories"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
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
    public function getStoriesBySlug($slug)
    {
        try {
            // Template
            $main_table = "stories";
            $story = Template::getSelectTemplate("story_card", null);
            $props = Template::getSelectTemplate("properties", $main_table);

            $str = Stories::selectRaw($story.",".$props.",is_finished,$main_table.id,story_type, date_start, date_end, story_result, story_location, story_tag, story_detail, story_stats, story_reference")
                ->leftjoin('admins', 'admins.id', '=', 'stories.created_by')
                ->leftjoin('users', 'users.id', '=', 'stories.created_by')
                ->groupBy($main_table.'.id')
                ->where('slug_name', $slug)
                ->first();
        
            if($str){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", $main_table, null), 
                    'status' => 'success',
                    'data' => $str
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("failed_found", $main_table, $slug), 
                    'status' => 'failed',
                    'data' => $str
                ], Response::HTTP_OK);
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
     *     path="/api/stories/limit/{limit}/order/{order}",
     *     summary="Show all stories with pagination and ordering",
     *     tags={"Stories"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of stories per page"
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
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="stories not found"
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
    public function getAllStories($limit, $order)
    {
        try {
            // Template
            $main_table = "stories";
            $story = Template::getSelectTemplate("story_card", null);
            $props = Template::getSelectTemplate("properties", $main_table);

            $str = Stories::selectRaw($story.",".$props)
                ->leftjoin('admins', 'admins.id', '=', 'stories.created_by')
                ->leftjoin('users', 'users.id', '=', 'stories.created_by')
                ->groupBy($main_table.'.id')
                ->orderBy($main_table.'.created_at', $order);

            $str = $str->paginate($limit);
        
            if($str->total() > 0){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", $main_table, null), 
                    'status' => 'success',
                    'data' => $str
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", $main_table, null),
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
     *     path="/api/stories/type/{type}/creator/{creator}",
     *     summary="Show all similiar stories by type and creator",
     *     tags={"Stories"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="battle"
     *         ),
     *         description="Type of the story"
     *     ),
     *     @OA\Parameter(
     *         name="creator",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="jhon"
     *         ),
     *         description="Name of the story creator"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
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
    public function getSimiliarStories($type, $creator)
    {
        try {
            // Template
            $main_table = "stories";
            $story = Template::getSelectTemplate("story_card", null);
            $props = Template::getSelectTemplate("properties", $main_table);

            $str = Stories::selectRaw($story.",".$props)
                ->leftjoin('admins', 'admins.id', '=', 'stories.created_by')
                ->leftjoin('users', 'users.id', '=', 'stories.created_by')
                ->groupBy($main_table.'.id')
                ->orderBy($main_table.'.created_at', "ASC")
                ->where('story_type', $type)
                ->orWhere($main_table.'.created_by', $creator)
                ->limit(12)
                ->get();
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", $main_table, null), 
                'status' => 'success',
                'data' => $str
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something wrong. please contact admin',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stories/top/rate",
     *     summary="Show 7 best rate stories",
     *     tags={"Stories"},
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="stories not found"
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
    public function getBestRatedStories()
    {
        try {
            $res = Stories::selectRaw('main_title, story_type, CAST(AVG(rate) as UNSIGNED) as average_rate, COUNT(1) as total_rate, admins.username as admin_username, users.username as user_username, stories.created_at')
                ->leftjoin('admins', 'admins.id', '=', 'stories.created_by')
                ->leftjoin('users', 'users.id', '=', 'stories.created_by')
                ->leftjoin('feedbacks', 'feedbacks.stories_id', '=', 'stories.id')
                ->groupby('stories.id')
                ->orderby('average_rate', "DESC")
                ->limit(7)
                ->get();

            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", "stories", null), 
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", "stories", null), 
                    'status' => 'failed',
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
     *     path="/api/stories/top/discuss",
     *     summary="Show 7 most discussed stories",
     *     tags={"Stories"},
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="stories not found"
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
    public function getMostDiscussStories()
    {
        try {
            $res = Stories::selectRaw('main_title, story_type, COUNT(1) as total_discuss, admins.username as admin_username, users.username as user_username, stories.created_at')
                ->leftjoin('admins', 'admins.id', '=', 'stories.created_by')
                ->leftjoin('users', 'users.id', '=', 'stories.created_by')
                ->leftjoin('discussions', 'discussions.stories_id', '=', 'stories.id')
                ->groupby('stories.id')
                ->orderby('total_discuss', "DESC")
                ->limit(7)
                ->get();

            if($res){
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read", "stories", null), 
                    'status' => 'success',
                    'data' => $res
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_read_empty", "stories", null), 
                    'status' => 'failed',
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

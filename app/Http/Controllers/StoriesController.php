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
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
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

            $str = Stories::selectRaw($story.",".$props.",is_finished, story_type, date_start, date_end, story_result, story_location, story_tag, story_detail, story_stats, story_reference")
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stories/limit/{page_limit}/order/{order}",
     *     summary="Show all stories with pagination and ordering",
     *     tags={"Stories"},
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllStories($page_limit, $order)
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

            $str = $str->paginate($page_limit);
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", $main_table, null), 
                'status' => 'success',
                'data' => $str
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stories/type/{type}/creator/{creator}",
     *     summary="Show all similiar stories by type and creator",
     *     tags={"Stories"},
     *     @OA\Response(
     *         response=200,
     *         description="stories found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
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
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

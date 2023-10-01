<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Books;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBook(Request $request)
    {
        try {
            $validator = Validation::getValidateBook($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    "msg" => $errors, 
                    "status" => 422
                ]);
            } else {
                $check = Books::selectRaw('1')->where('title', $request->title)->first();
                
                if($check == null){
                    $uuid = Generator::getUUID();
                    Books::create([
                        'id' => $uuid,
                        'title' => $request->title,
                        'author' => $request->author,
                        'reviewer' => $request->reviewer,
                        'review_date' => $request->review_date,
                    ]);
            
                    return response()->json([
                        'message' => "'".$request->title."' Data Created", 
                        'status' => 'success'
                    ], Response::HTTP_OK);
                }else{
                    return response()->json([
                        "message" => "Data is already exist", 
                        "status" => 'failed'
                    ]);
                }
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllBooks($page_limit, $order){
        try {
            $bok = Books::select('id', 'title', 'author', 'reviewer', 'review_date')
                ->orderBy('title', $order)
                ->paginate($page_limit);
        
            return response()->json([
                'message' => count($bok)." Data retrived", 
                'status' => 'success',
                'data' => $bok
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalBooksByReviewer($limit){
        try {
            $bok = Books::selectRaw('reviewer as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
                'message' => count($bok)." Data retrived", 
                'status' => 'success',
                'data' => $bok
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalBooksByYearReview(){
        try {
            $bok = Books::selectRaw('YEAR(datetime) as year_review, count(*) as total')
                ->whereRaw('YEAR(datetime) is not null')
                ->groupBy('year_review')
                ->orderBy('year_review', 'ASC')
                ->get();
        
            return response()->json([
                'message' => count($bok)." Data retrived", 
                'status' => 'success',
                'data' => $bok
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateBookById(Request $request, $id){
        try {
            $validator = Validation::getValidateBook($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'message' => $errors, 
                    'status' => 'failed'
                ]);
            } else {
                Books::where('id', $id)->update([
                    'title' => $request->title,
                    'author' => $request->author,
                    'reviewer' => $request->reviewer,
                    'review_date' => $request->review_date,
                ]);
        
                return response()->json([
                    'message' => "'".$request->title."' Data Updated", 
                    'status' => 'success'
                ], Response::HTTP_OK);
            }
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteBookById($id){
        try {
            $bok = Books::selectRaw("concat ('The Book ', title, ' by ', author) as final_name")
                ->where('id', $id)
                ->first();
                Books::destroy($id);

            return response()->json([
                'message' => "'".$bok->final_name."' Data Destroyed", 
                'status' => 'success'
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

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
                    "message" => $errors, 
                    "status" => 'error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => "1",
                        'updated_at' => null,
                        'updated_by' => null,
                    ]);
            
                    return response()->json([
                        "message" => Generator::getMessageTemplate("api_create", "book", $request->title), 
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

    public function getAllBooks($page_limit, $order, $search){
        try {
            $search = trim($search);

            $bok = Books::select('id', 'title', 'author', 'reviewer', 'review_date')
                ->orderBy('title', $order);
            
            // Filtering
            if($search != "" && $search != "%20"){
                $bok = $bok->where('title', 'LIKE', '%' . $search . '%')
                    ->orwhere('author', 'LIKE', '%' . $search . '%')
                    ->orwhere('reviewer', 'LIKE', '%' . $search . '%');
            }

            $bok = $bok->paginate($page_limit);
        
            return response()->json([
                'message' => Generator::getMessageTemplate("api_read", "book", null), 
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
                //'message' => count($bok)." Data retrived", //masih belum clear
                'message' => Generator::getMessageTemplate("api_read", 'book', null),
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
                //'message' => count($bok)." Data retrived",  //masih belum clear
                'message' => Generator::getMessageTemplate("api_read", 'book', null),
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
                ], Response::HTTP_BAD_REQUEST);
            } else {
                Books::where('id', $id)->update([
                    'title' => $request->title,
                    'author' => $request->author,
                    'reviewer' => $request->reviewer,
                    'review_date' => $request->review_date,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => null,
                ]);
        
                return response()->json([
                    'message' => Generator::getMessageTemplate("api_update", "book", $request->title), //masi belum fix
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
            $bok = Books::selectRaw("concat (title, ' by ', author) as final_name")
                ->where('id', $id)
                ->first();

            Books::destroy($id);

            return response()->json([
                'message' => Generator::getMessageTemplate("api_delete", "book", $bok->final_name),
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

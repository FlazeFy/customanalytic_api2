<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\Validation;
use App\Helpers\Generator;

use App\Models\Books;
use App\Models\Histories;

class BooksController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/books",
     *     summary="Add book",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="New book ... has been created"
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
                $msg = Generator::getMessageTemplate("api_create", "book", $request->title);
                $data = new Request();
                $obj = [
                    'type' => "book",
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
                    $check = Books::selectRaw('1')->where('title', $request->title)->first();
                    
                    if($check == null){
                        $uuid = Generator::getUUID();
                        $user_id = $request->user()->id;

                        Books::create([
                            'id' => $uuid,
                            'title' => $request->title,
                            'author' => $request->author,
                            'reviewer' => $request->reviewer,
                            'review_date' => $request->review_date,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_by' => $user_id,
                            'updated_at' => null,
                            'updated_by' => null,
                        ]);

                        Histories::create([
                            'id' => Generator::getUUID(),
                            'history_type' => $data->type, 
                            'body' => $data->body,
                            'created_at' => date("Y-m-d H:i:s"),
                            'created_by' => '1' // for now
                        ]);
                
                        return response()->json([
                            "message" => $msg, 
                            'status' => 'success'
                        ], Response::HTTP_OK);
                    }else{
                        return response()->json([
                            "message" => "Data is already exist", 
                            "status" => 'failed'
                        ], Response::HTTP_CONFLICT);
                    }
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
     *     path="/api/books/limit/{limit}/order/{order}/find/{search}",
     *     summary="Show all books with pagination, ordering, and search",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of book per page"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="asc"
     *         ),
     *         description="Order by field title"
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Peter"
     *         ),
     *         description="Search term based on the field author or reviewer"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="books found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getAllBooks($limit, $order, $search){
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

            $bok = $bok->paginate($limit);
        
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

    /**
     * @OA\GET(
     *     path="/api/books/total/byreviewer/{limit}",
     *     summary="Total book by reviewer",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         ),
     *         description="Number of book per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="book found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalBooksByReviewer($limit){
        try {
            $bok = Books::selectRaw('reviewer as context, count(*) as total')
                ->groupByRaw('1')
                ->orderBy('total', 'DESC')
                ->limit($limit)
                ->get();
        
            return response()->json([
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

    /**
     * @OA\GET(
     *     path="/api/books/total/byyearreview",
     *     summary="Total book by year reviewe",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="book found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getTotalBooksByYearReview(){
        try {
            $bok = Books::selectRaw('YEAR(review_date) as year_review, count(*) as total')
                ->whereRaw('YEAR(review_date) is not null')
                ->groupBy('year_review')
                ->orderBy('year_review', 'ASC')
                ->get();
        
            return response()->json([
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

    /**
     * @OA\GET(
     *     path="/api/books",
     *     summary="Show all book module or combined API (all data & stats)",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="limit_data_all",
     *         in="query",
     *         description="Limit the number of books to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order_data_all",
     *         in="query",
     *         description="Order the books by ascending or descending",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             default="asc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_data_all",
     *         in="query",
     *         description="Search term for filtering books",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             default="%20"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit_stats_by_reviewer",
     *         in="query",
     *         description="Limit the number of reviewers to show",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=7
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="book module found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function getBooksModule(Request $request){
        try {
            $data_all = json_decode(
                $this->getAllBooks(
                    $request->limit_data_all ?? 20,
                    $request->order_data_all ?? 'asc',
                    $request->search_data_all ?? '%20'
                )->getContent(), true)['data'];
    
            $total_by_reviewer = json_decode(
                $this->getTotalBooksByReviewer(
                    $request->limit_stats_by_reviewer ?? 7
                )->getContent(), true)['data'];

            $total_by_year_review = json_decode(
                $this->getTotalBooksByYearReview()->getContent(), true)['data'];

            return response()->json([
                "message" => Generator::getMessageTemplate("api_read", 'book module', null),
                "status" => 'success',
                "data_all" => $data_all,
                "stats" => [
                    "total_by_reviewer" => $total_by_reviewer,
                    "total_by_year_review" => $total_by_year_review,
                ]
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/books/{id}",
     *     summary="Update book by id",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="book ... has been updated"
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
                $msg = Generator::getMessageTemplate("api_update", "book", $request->title);
                $data = new Request();
                $obj = [
                    'type' => "book",
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
                    Books::where('id', $id)->update([
                        'title' => $request->title,
                        'author' => $request->author,
                        'reviewer' => $request->reviewer,
                        'review_date' => $request->review_date,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => null,
                    ]);

                    Histories::create([
                        'id' => Generator::getUUID(),
                        'history_type' => $data->type, 
                        'body' => $data->body,
                        'created_at' => date("Y-m-d H:i:s"),
                        'created_by' => '1' // for now
                    ]);
            
                    return response()->json([
                        'message' => $msg, //masi belum fix
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
     * @OA\DELETE(
     *     path="/api/books/{id}",
     *     summary="Delete book by id",
     *     tags={"Books"},
     *     @OA\Response(
     *         response=200,
     *         description="book ... has been updated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function deleteBookById($id){
        try {
            $bok = Books::selectRaw("concat (title, ' by ', author) as final_name")
                ->where('id', $id)
                ->first();
            
            $msg = Generator::getMessageTemplate("api_delete", "book", $bok->final_name);
            $data = new Request();
            $obj = [
                'type' => "book",
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
                Books::destroy($id);

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
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

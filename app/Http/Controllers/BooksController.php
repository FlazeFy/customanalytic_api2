<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Validation;

use App\Models\Books;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAllBooks($page_limit, $order){
        $bok = Books::select('id', 'title', 'author', 'reviewer', 'review_date')
            ->orderBy('title', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }

    public function getTotalBooksByReviewer($limit){
        $bok = Books::selectRaw('reviewer as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get();
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }

    public function getTotalBooksByYearReview(){
        $bok = Books::selectRaw('YEAR(datetime) as year_review, count(*) as total')
            ->whereRaw('YEAR(datetime) is not null')
            ->groupBy('year_review')
            ->orderBy('year_review', 'ASC')
            ->get();
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }

    public function updateBookById(Request $request, $id){
        $validator = Validation::getValidateBook($request);

        if ($validator->fails()) {
            $errors = $validator->messages();

            return response()->json([
                "msg" => $errors, 
                "status" => 422
            ]);
        } else {
            Books::where('id', $id)->update([
                'title' => $request->title,
                'author' => $request->author,
                'reviewer' => $request->reviewer,
                'review_date' => $request->review_date,
            ]);
    
            return response()->json([
                "msg" => "'".$request->title."' Data Updated", 
                "status" => 200
            ]);
        }
    }

    public function deleteBookById($id){
        $bok = Books::selectRaw("concat ('The Book ', title, ' by ', author) as final_name")
            ->where('id', $id)
            ->first();
            Books::destroy($id);

        return response()->json([
            "msg"=> "'".$bok->final_name."' Data Destroyed", 
            "status"=>200
        ]);
    }
}

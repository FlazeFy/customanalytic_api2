<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $bok = Books::select('id', 'title', 'author', 'reviewer', 'review_date', 'datetime')
            ->orderBy('title', $order)
            ->paginate($page_limit);
    
        return response()->json([
            "msg"=> count($bok)." Data retrived", 
            "status"=>200,
            "data"=>$bok
        ]);
    }

    public function getTotalBooksByReviewer(){
        $bok = Books::selectRaw('reviewer as context, count(*) as total')
            ->groupByRaw('1')
            ->orderBy('total', 'DESC')
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
}

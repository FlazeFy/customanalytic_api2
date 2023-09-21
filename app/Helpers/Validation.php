<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Validator;

class Validation
{
    public static function getValidateLogin($request){
        return Validator::make($request->all(), [
            'username' => 'required|min:6|max:30|string',
            'password' => 'required|min:6|string'
        ]);
    }

    public static function getValidateAircraft($request){
        return Validator::make($request->all(), [
            'name' => 'required|max:36|string',
            'primary_role' => 'required|max:36|string',
            'manufacturer' => 'required|max:75|string',
            'country' => 'required|max:36|string',
        ]);
    }

    public static function getValidateBook($request){
        return Validator::make($request->all(), [
            'title' => 'required|max:144|string',
            'author' => 'required|max:75|string',
            'reviewer' => 'required|max:75|string',
            'review_date' => 'required|date_format:Y-m-d',
        ]);
    }

    public static function getValidateEvent($request){
        return Validator::make($request->all(), [
            'event' => 'required|max:75|string',
            'date_start' => 'required|date_format:Y-m-d',
            'date_end' => 'nullable|date_format:Y-m-d',
        ]);
    }

    public static function getValidateShips($request){
        return Validator::make($request->all(), [
            'name' => 'required|max:75|string',
            'class' => 'required|max:75|string',
            'country' => 'required|max:36|string',
            'launch_year' => 'required|max:4|string',
        ]);
    }

    public static function getValidateVehicle($request){
        return Validator::make($request->all(), [
            'name' => 'required|max:75|string',
            'primary_role' => 'required|max:36|string',
            'manufacturer' => 'required|max:144|string',
            'country' => 'required|max:36|string',
        ]);
    }

    public static function getValidateWeapon($request){
        return Validator::make($request->all(), [
            'name' => 'required|max:75|string',
            'type' => 'required|max:36|string',
            'country' => 'required|max:36|string',
        ]);
    }
}

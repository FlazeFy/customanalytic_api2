<?php
namespace App\Helpers;
use App\Models\User;

class Generator
{
    public static function getUUID(){
        $result = '';
        $bytes = random_bytes(16);
        $hex = bin2hex($bytes);
        $time_low = substr($hex, 0, 8);
        $time_mid = substr($hex, 8, 4);
        $time_hi_and_version = substr($hex, 12, 4);
        $clock_seq_hi_and_reserved = hexdec(substr($hex, 16, 2)) & 0x3f;
        $clock_seq_low = hexdec(substr($hex, 18, 2));
        $node = substr($hex, 20, 12);
        $uuid = sprintf('%s-%s-%s-%02x%02x-%s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $clock_seq_low, $node);
        
        return $uuid;
    }

    public static function getMessageTemplate($type, $ctx, $obj){
        if($obj != null){
            $obj = "called ".$obj;
        } else {
            $obj = "";
        }

        if($type == "lost_session"){
            $res = "Session lost, please sign in again";
        } else if($type == "api_create"){ 
            $res = "New ".$ctx." ".$obj." has been created";
        } else if($type == "api_create_failed"){ 
            $res = "Failed to create ".$ctx;
        } else if($type == "api_read"){ 
            $res = $ctx." found";
        } else if($type == "api_read_failed"){
            $res = $ctx." not found";
        } else if($type == "api_update"){ 
            $res = "New ".$ctx." ".$obj." has been updated";
        } else if($type == "api_update_failed"){ 
            $res = "Failed to update ".$ctx;
        } else if($type == "api_delete"){ 
            $res = "New ".$ctx." ".$obj." has been deleted";
        } else if($type == "api_delete_failed"){ 
            $res = "Failed to delete ".$ctx;
        } else if($type == "duplicate_data"){ 
            $res = $ctx." already exist ";
        } else if($type == "failed_auth"){ 
            $res = "Lost authentication, please sign in again";
        } else if($type == "failed_found"){ 
            $res = $ctx." with ".$obj." has not found";
        } else if($type == "custom"){
            $res = $ctx;
        } else {
            $res = "Failed to get respond message";
        }

        return ucfirst(trim($res));
    }

    public static function getRandomYear(){
        $now = (int)date("Y");
        $res = $now + mt_rand(-3, 6); 
        
        return $res;
    }

    public static function getRandomDate($is_null, $format){

        if ($is_null == 1){
            $res = null;
        } else {
            $start = strtotime('2018-01-01 00:00:00');
            $end = strtotime(date("Y-m-d H:i:s"));
            $random = mt_rand($start, $end); 

            if($format == 'datetime'){
                $res = date('Y-m-d H:i:s', $random);
            } else if ($format == 'date'){
                $res = date('Y-m-d', $random);
            }
        }
        return $res;
    }

    public static function getRandomUser($null){
        if($null == 0){
            $user = User::inRandomOrder()->take(1)->get();

            foreach($user as $us){
                $res = $us->id;
            }
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function getRandomRoleType(){
        return; 
    }

    public static function getRandomCountry(){
        return; 
    }
    
    public static function getRandomCoordinate(){
        return; 
    }
}


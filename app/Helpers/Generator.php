<?php
namespace App\Helpers;

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
}


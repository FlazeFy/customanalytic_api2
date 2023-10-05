<?php
namespace App\Helpers;

class Template
{
    public static function getDataLength($type){ 
        if($type == "mini_char"){
            return 12;
        } else if($type == "exshort_char"){
            return 25;
        } else if($type == "short_char"){
            return 36;
        } else if($type == "med_char"){
            return 75;
        } else if($type == "exmed_char"){
            return 144;
        } else if($type == "large_char"){
            return 255;
        }
    }
}
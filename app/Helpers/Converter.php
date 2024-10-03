<?php
namespace App\Helpers;

class Converter
{
    public static function calculateDistance($coord1, $coord2) {
        $earthRadius = 6371000; // fixed in meters
        list($lat1, $lon1) = explode(',', $coord1);
        list($lat2, $lon2) = explode(',', $coord2);
    
        $latRad1 = deg2rad(floatval($lat1));
        $lonRad1 = deg2rad(floatval($lon1));
        $latRad2 = deg2rad(floatval($lat2));
        $lonRad2 = deg2rad(floatval($lon2));
    
        $latDiff = $latRad2 - $latRad1;
        $lonDiff = $lonRad2 - $lonRad1;
    
        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latRad1) * cos($latRad2) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $res = $earthRadius * $c;
    
        return $res; // in meters
    }
}
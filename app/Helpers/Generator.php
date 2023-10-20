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

    public static function getRandomUser($is_null){
        if($is_null == 0){
            $user = User::inRandomOrder()->take(1)->get();

            foreach($user as $us){
                $res = $us->id;
            }
        } else {
            $res = null;
        }
        
        return $res;
    }

    public static function getRandomID($ctx){
        
        return;
    }

    public static function getRandomRoleType($ctx){
        if($ctx == "airplane"){
            $coll = ['Medium Bomber', 'Light Bomber', 'Seaplane', 'Reconnaissance Aircraft',
                'Heavy Bomber', 'Other', 'Transport', 'Fighter', 
                'Torpedo Bomber', 'Heavy Fighter', 'Jet Fighter', 'Glider',
                'Biplane Fighter', 'Prototype Aircraft', 'Night Fighter', 'Ground Attack Aircraft', 'Dive Bomber'
            ];
        } else if($ctx == "facilities"){
            $coll = ['Shipyard', 'Airfield', 'Prison Camp', 'Military Headquarters',
                'Government Building', 'Factory, Shipyard', 'Fortification', 'Other',
                'Factory', 'Army Base', 'Government Building, Prison Camp', 'Naval Port',
                'Airfield, Naval Port', 'Airfield, Factory', 'Airfield, Naval Port, Shipyard',
                'Airfield, Army Base, Naval Port, Shipyard', 'Shipyard', 'Army Base, Prison Camp', 'Shipyard, Naval Port',
                'Airfield, Army Base', 'Naval Port, Shipyard', 'Prison Camp, Factory'
            ];
        } else if($ctx == "ships"){
            $coll = ['No Classification', 'Deutschland-class Heavy Cruiser', 'Balao-class Submarine', 'Bagley-class Destroyer',
                'Essex-class Aircraft Carrier', 'Ranger-class Aircraft Carrier', 'Gato-class Submarine', 'R-class Merchant Vessel',
                'Renown-class Battlecruiser', 'Renraku-tei-class Motor Torpedo Boat', 'Fletcher-class Destroyer', 'Revenge-class Battleship',
                'Clemson-class Destroyer', 'Richelieu-class Battleship', 'Hans Albrecht Wedel-class Seaplane Tender', 'Type L3-class Submarine',
                'Nelson-class Battleship', 'Vittorio Veneto-class Battleship', 'Tench-class Submarine', 'Ryuho-class Aircraft Carrier', 
                'Ryujo-class Aircraft Carrier', 'S-class Submarine', 'Admiral Hipper-class Heavy Cruiser', 'Srednyaya-class Submarine',
                'Saar-class Submarine Tender' 
            ];
        } else if($ctx == "vehicles"){
            $coll = ['Other', 'Tankette', 'Armored Car', 'Light Tank',
                'Medium Tank', 'Heavy Tank', 'Motorcycle', 'Transport',
                'Tank Destroyer', 'Self-Propelled Gun', 'Assault Gun', 'Self-Propelled Rocket Artillery',
                'Artillery Tractor', 'Cruiser Tank', 'Infantry Tank'
            ];
        } else if($ctx == "weapons"){
            $coll = ['GermanyÂ Â', 'Field Gun', 'Recoilless Gun', 'Anti-Tank Gun',
                'Anti-Aircraft Gun', 'Launcher', 'Coastal Defense Gun', 'Railway Gun',
                'Air Raid Shelter', 'Rifle', 'Submachine Gun', 'Blade',
                'Handgun', 'Anti-Tank Rifle', 'Machine Gun', 'Grenade',
                'Communications', 'Torpedo', 'Missile', 'Naval Gun',
                'Bombsight', 'Other Weapons', 'Headgear', 'Aircraft Autocannon',
                'Uniform', 'Munitions Fuze', 'Shotgun', 'Submachine Gun'
            ];
        } else if($ctx == "users"){ 
            $coll = ['visitor', 'creator'];
        } else if($ctx == "stories"){ 
            $coll = ['battle', 'biography'];
        } else if($ctx == "histories"){ 
            $coll = ['stories', 'user', 'admin', 'weapons', 'vehicles', 'ships', 'facilities', 'airplane'];
        }

        $idx = array_rand($coll);
        $res = $coll[$idx];

        return $res;
    }

    public static function getRandomCountry(){
        $coll = ['France', 'Japan', 'United Kingdom', 'Germany', 
            'United States', 'Italy', 'Netherlands', 'Russia', 
            'Romania', 'Yugoslavia', 'Poland', 'Australia', 
            'China', 'Czechoslovakia', 'Canada', 'British Western Pacific Territories',
            'Taiwan', 'Dutch East Indies', 'China', 'Poland', 
            'India', 'Korea', 'Burma', 'Lithuania',
            'Philippines', 'Austria', 'US Pacific Islands', 'Australian New Guinea', 
            'Panama', 'Greenland', 'Malta', 'Singapore',
            'US Virgin Islands', 'Hong Kong', 'Norway', 'Switzerland',
            'Belgium'
        ];

        $idx = array_rand($coll);
        $res = $coll[$idx];

        return $res; 
    }
    
    public static function getRandomCoordinate(){
        $lng = -180 + (mt_rand() / mt_getrandmax()) * 360;
        $lat = -90 + (mt_rand() / mt_getrandmax()) * 180;
        $res = $lat.", ".$lng;

        return $res; 
    }

    public static function getRandomLocation(){
        $coll = ['Somme River', 'Dunkirk Harbour', 'Pearl Harbour', 'Katyn Forest', 'Volga River'];

        $idx = array_rand($coll);
        $res = $coll[$idx];

        return $res; 
    }

    public static function getRandomTag(){
        return; 
    }

    public static function getRandomReference(){
        return; 
    }
}


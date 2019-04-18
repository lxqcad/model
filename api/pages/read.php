<?php
// required headers
header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set('Asia/Kolkata');
 
// include database and object files
require_once '../model/db.php';
require_once '../model/utils.php';
 
// instantiate database and product object
$database = new Database();
 
$device_id =  getValue("device_id"); 

// query products
$stmt = $database->read($device_id);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num > 0){
 
    // products array
    $locations_arr=array();
    $locations_arr["records"]=array();
    $curtime = time();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        //extract($row);
        $cur_status = "ONLINE";
        $last_update = getTimeText($row['last_update'], $curtime);

        $time = strtotime($row['last_update']);

        if(($curtime-$time) > 180) {     //180 seconds 3 minutes
          //do stuff
          $cur_status = "OFFLINE";
        }

        $location_item=array(
            "id" => $row['id'],
            "last_update" => $last_update,
            //"description" => html_entity_decode($description),
            "device_name" => $row['device_name'],
            "longitude" => $row['longitude'],
            "latitude" => $row['latitude'],
            "device_status" => $cur_status
        );
 
        array_push($locations_arr["records"], $location_item);
    }
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data in json format
    echo json_encode($locations_arr);
}
 
else {
 
    // set response code - 404 Not found
    http_response_code(404);
 
    // tell the user no products found
    echo json_encode(
        array("message" => "No products found.")
    );
}
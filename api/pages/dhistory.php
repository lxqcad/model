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
$limit = getValue("limit",false); 
if(is_null($limit)) {
    $limit = 0;
}
// initialize object
 
// query products
$stmt = $database->dhistory($device_id, $limit);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num > 0) {
 
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
        $last_update = getTimeText($row['date_time'], $curtime);

        $location_item=array(
            "date_time" => date("H:i:s", strtotime($row['date_time'])),
            "date_time_remark" => $last_update,
            "latitude" => $row['latitude'],
            "longitude" => $row['longitude'],
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
        array("message" => "No records found.")
    );
}
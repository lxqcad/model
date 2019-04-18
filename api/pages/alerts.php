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

$stmt = $database->alerts($device_id);
$num = $stmt->rowCount();

if($num > 0){

    // products array
    $alerts_arr=array();
    $alerts_arr["records"]=array();
    $curtime = time();
    
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $last_update = getTimeText($row['date_time'], $curtime);

        $alert_type = ($row['exit_entry'] == 0) ? "SOS" : ($row['exit_entry'] == 1 ? "FENCE IN":"FENCE OUT");
        $fence_name = (is_null($row['fence_name'])) ? "Munshipulia, Lucknow" : $row['fence_name'];
        $alert_item=array(
            "fence_name" => html_entity_decode($fence_name),
            "date_time" => date("d/m/Y h:i:sa", strtotime($row['date_time'])),
            "date_time_remark" => $last_update,
            "alert" => $alert_type,
        );
    
        array_push($alerts_arr["records"], $alert_item);
    }
    
    // set response code - 200 OK
    http_response_code(200);
    
    // show products data in json format
    echo json_encode($alerts_arr);
}        
else {

    // set response code - 404 Not found
    http_response_code(404);
    
    // tell the user no products found
    echo json_encode(
        array("message" => "No fencing added yet.", "records" => null)
    );
}

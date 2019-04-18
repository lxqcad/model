<?php
  $salt1 = "qm&h*bZ";
  $salt2 = "pg!A@M";
  $app_root = "http://localhost/modelapp";

function hashPassword($password) {
    global $salt1, $salt2;
    return hash('SHA1', "$salt1$password$salt2");
}

function getValue($value_name, $required = true, $default = null) {
    if (!empty($_REQUEST[$value_name])) {
        return filter_var($_REQUEST[$value_name], FILTER_SANITIZE_STRING);
    } 
    else {  
        if($required) {
            http_response_code(400);

            // tell the user no products found
            echo json_encode(
                array("message" => "Required parameter '$value_name'.")
            );
            die();
        }
        else {
            return $default;
        }
    }
}

function getTimeText($time, $curtime=null) {
    $time = strtotime($time);
    if($curtime == null) $curtime = time();

    $time_diff = ($curtime-$time);
    $last_update = "";
    $passcount = 0;

    switch($time_diff) {
        case ($time_diff > 86400):
            $ncount = intdiv($time_diff,86400);
            $last_update .= $ncount." day(s) ";
            $time_diff = ($time_diff % 86400);
            $passcount++;
            if($passcount == 2 || $ncount > 1) break;
        case ($time_diff > 3600):
            $ncount = intdiv($time_diff,3600);
            if($ncount == 0) break;
            $last_update .= $ncount." hour(s) ";
            $time_diff = ($time_diff % 3600);
            $passcount++;
            if($passcount == 2 || $ncount > 1) break;
        case ($time_diff > 60):
            $ncount = intdiv($time_diff,60);
            if($ncount == 0) break;
            $last_update .= $ncount." min(s) ";
            $time_diff = ($time_diff % 60);
            $passcount++;
            if($passcount == 2 || $ncount > 1) break;
        default:
            if($time_diff == 0) break;
            $last_update .= $time_diff." second(s) ";
    }
    $last_update .= "ago.";
    return ($last_update);
}
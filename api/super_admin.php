<?php
    if(!isset($session_user_type)) {
        echo "You cannot directly access this page.";
        die();
    }

    $database = new Database();

    switch($redirect) {
        case "user_fetch":
            $edit_id = getValue("row_id");
            $stmt = $database->execute("SELECT id as row_id, user_name, user_email, user_type FROM tmp_usr WHERE id=".$edit_id);

            http_response_code(200);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results_arr["status"] = 'success';

                $results_arr["user_name"] = $row['user_name'];
                $results_arr["user_email"] = $row['user_email'];
                $results_arr["user_type"] = $row['user_type'];
                
                // show products data in json format
                echo json_encode($results_arr);
            }
            else {
                $results_arr["status"] = 'error';
                $results_arr["message"] = 'Unable to fetch record.';

                echo json_encode($results_arr);
            }
            break;
        case "user_add":
        case "user_edit":
            $user_name = getValue("user_name");
            $user_email = getValue("user_email");
            $user_type = getValue("user_type");
            $extra_condition = "";
            if($redirect === 'user_edit') {
                $edit_id = getValue("row_id");
                $extra_condition = " AND id <> $edit_id";
            }
            else {
                $user_password = hashPassword(getValue("user_password"));
            }
            $stmt = $database->execute("SELECT id FROM tmp_usr WHERE user_email='$user_email' $extra_condition");
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                http_response_code(200);
                
                // tell the user email found
                echo json_encode(
                    array("status" => "error", "message" => "Email address already exists.", "records" => "")
                );
                break;
            }
            else {
                if($redirect === 'user_edit')
                    $stmt = $database->execute("UPDATE tmp_usr SET user_name='$user_name', user_email='$user_email', user_type=$user_type WHERE id=".$edit_id);
                else
                    $stmt = $database->execute("INSERT INTO tmp_usr (user_name, user_email, user_password, user_type) VALUES ('$user_name', '$user_email', '$user_password', $user_type)");
                $num = $stmt->rowCount();
                if($num) {
                    http_response_code(200);

                    echo json_encode(
                        array("status" => "success", "message" => ".", "records" => "")
                    );
                }
                else {
                    http_response_code(400);
                    echo json_encode(
                        array("message" => "Record could not be modified.", "records" => "")
                    );
                }
            }
            break;
        case "user_delete":
            $del_id =  getValue("del_id");
            $stmt = $database->execute("DELETE FROM tmp_usr WHERE id IN ($del_id)");
            $num = $stmt->rowCount();
            
            if($num){
            
            }
            else {
                http_response_code(400);
                
                // tell the user no products found
                echo json_encode(
                    array("message" => "Records could not be deleted.", "records" => null)
                );
                break;
            }
            //break;
        case "user_list":
            $page_limit = 10;
            $page_num =  getValue("pgno", false, 1);
            $search_text = getValue("search", false, '');
            $option = getValue("option", false, '');
            if($search_text != '') {
                $search_text = " WHERE user_name LIKE '%$search_text%' OR user_email LIKE '%$search_text%'";  // *** remove WHERE clause if main query includes it
            }

            if($option == 'recent') {
                $search_text = " WHERE last_update >= NOW() - INTERVAL 1 DAY ORDER BY last_update DESC";
                $page_start = 0;
                $page_limit = 5;
            }
            else {
                $stmt = $database->execute("SELECT COUNT(*) AS total_records FROM tmp_usr $search_text");
                $results_arr = array();
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $total_records = $row['total_records'];
                    $total_pages = intval($total_records / $page_limit) + (($total_records % $page_limit) ? 1:0);
                    if($page_num < 1)
                        $page_num = 1;
                    elseif($page_num > $total_pages)
                        $page_num = $total_pages;

                    $results_arr["cur_page"] = $page_num;
                    $page_start = ($page_num - 1) * $page_limit;
                    $results_arr["total_pages"] = $total_pages;
                    $results_arr["page_start"] = $page_start;
                    $results_arr["total_records"] = $total_records;
                }
                else {
                    http_response_code(404);
                    
                    // tell the user no products found
                    echo json_encode(
                        array("message" => "No records found.", "records" => null)
                    );
                }
            }

            $stmt = $database->execute("SELECT id as row_id, user_name, user_email, user_type FROM tmp_usr $search_text LIMIT $page_start, $page_limit");
            $num = $stmt->rowCount();
            
            if($num > 0){
            
                $results_arr["records"]=array();
                $results_arr["text_align"]=array('', 'left', 'left', 'center');
                //$curtime = time();
                
                // retrieve our table contents
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    //$last_update = getTimeText($row['date_time'], $curtime);
            
                    $user_type = ($row['user_type'] == 1) ? "Super Admin" : ($row['user_type'] == 2 ? "Admin":"Faculty");
                    $alert_item=array(
                        "row_id" => $row['row_id'],
                        "user_name" => html_entity_decode($row['user_name']),
                        //"date_time" => date("d/m/Y h:i:sa", strtotime($row['date_time'])),
                        "user_email" => $row['user_email'],
                        "user_type" => $user_type,
                    );

                    array_push($results_arr["records"], $alert_item);
                }

                $results_arr["page_limit"] = $num;
                // set response code - 200 OK
                http_response_code(200);
                
                // show products data in json format
                echo json_encode($results_arr);
            }        
            else {
            
                // set response code - 404 Not found
                http_response_code(404);
                
                // tell the user no products found
                echo json_encode(
                    array("message" => "No users found.", "records" => null)
                );
            }
        
            break;
        default:
            // set response code - 404 Not found
            http_response_code(404);
            
            // tell the user no products found
            echo json_encode(
                array("message" => "Invalid Link Specified.", "records" => null)
            );
    }

?>
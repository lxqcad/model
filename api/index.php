<?php 
  session_start();

  require_once '../app/model/utils.php';

  $redirect = getValue("function", true);
  if (isset($_SESSION['userid']))
  {
      $session_user_id = $_SESSION['userid'];
      $session_user_type = $_SESSION['usertype'];
  }
  else {
      $session_user_id = 0;
      $session_user_type = 0;
  }

  require_once '../app/model/db.php';

  if($session_user_type == 1) { // Super-Admin
      require "./super_admin.php";
  }
  elseif($session_user_type == 2) { // Admin
      require "./admin.php";
  }
  elseif($session_user_type == 3) { // Faculty
      require "./faculty.php";
  }
  else { // Not Logged
      echo "Restricted Area!! You cannot access this page => ";
      die();
  }

?>  
  
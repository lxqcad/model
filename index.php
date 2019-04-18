<?php 
  session_start();

  require_once './app/model/utils.php';

  $redirect = getValue("page", false, "home");
  $page_action = getValue("action", false, null);
  
  if (isset($_SESSION['userid']))
  {
      $logged_in = TRUE;
      $session_user_id = $_SESSION['userid'];
      $session_user_type = $_SESSION['usertype'];
  }
  else $logged_in = FALSE;

  $error_message = $message = null;
  if($redirect == "logout")  {
      if (isset($_SESSION['userid']))  { 
          $_SESSION=array();

          if (session_id() != "" || isset($_COOKIE[session_name()]))
            setcookie(session_name(), '', time()-2592000, '/');
      
          session_destroy();
          header("refresh:1;url=?page=home");
          echo "<div class='main'><br>" .
              "The system is logging you out.....</div>";
          die();
      }  
      else echo "<div class='main'><br>" .
          "You cannot log out because you are not logged in</div>";
  }
  if($page_action == "login")  {
      require_once './app/model/db.php';

      $database = new Database();
      $email = getValue('username');
      $password = getValue('password');
      $token = hashPassword($password);
      $stmt = $database->execute("select id, user_name, user_type from users where user_email='$email' and user_password='$token'");

      if($stmt->rowCount() >= 1)  {
          if($Results = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $message = $Results['user_name']." Login Sucessful!";
              //$_SESSION['pass'] = $token;
              $_SESSION['userid'] = $session_user_id = $Results['id'];
              $_SESSION['usertype'] = $session_user_type = $Results['user_type'];
              $logged_in = TRUE;
          }
      }
      else
      {
          $error_message = "Invalid email or password!!";
      }
  }

  require "./app/header.php";

  if($logged_in == true) {
      if($session_user_type == 1) {
          $page_open = "./app/admin_".$redirect.".php";
          if(file_exists($page_open)) {
              require "./app/menu.php"; 
              require $page_open;
          }
          else
              die("Invalid link specified");
      }
      elseif($session_user_type == 2) {
          echo "<div id='positive_message'></div>";
               //"<div class='ui info message' id='info_message'></div>".
               //"<div class='ui warning message' id='warning_message'></div>".
          echo "<div id='error_message'></div>";

          $page_open = "./app/page_".$redirect.".php";
          if(file_exists($page_open)) {
              require "./app/menu.php"; 
              require $page_open;
          }
          else
              die("Invalid link specified");
      }
  }
  else {
      echo ($message ? "<div class='ui positive message'>".$message."</div>" : "");
      //"<div class='ui info message' id='info_message'></div>".
      //"<div class='ui warning message' id='warning_message'></div>".
      echo ($error_message ? "<div class='ui error message'>".$error_message."</div>" : "");
      require "./app/login.php";
  }

?>

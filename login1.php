<?php
session_start();

if (isset($_POST['submitted']))
{
   require_once ('includes/login1_functions.inc.php');
   require_once ('connect.php');
   
   list ($check, $data) = check_login($dbc, $_POST['initials'], $_POST['pass']);
   if ($check)
   {
	  $_SESSION['initials'] = $data['initials'];
	  $_SESSION['pass'] = $data['pass'];
	  $url = absolute_url('choose1.php');
	  header("Location: $url");
	  exit();
   }
   else
   {
      $errors = $data;
   }
   mysqli_close($dbc);
}

include ('includes/login1_page.inc.php');
?>
<?php 
session_start();
if (isset($_GET["m"]) && $_GET="true")
{
   //Goto mobile site 
}


//////////////////////////////////////////////////////////////////////////////////////////////////
//VERSION 02_02                                                                                 //
//REVISED 201701211033                                                                         //
//This file is for use with version 02_02 revised 201701211033                                 //
//////////////////////////////////////////////////////////////////////////////////////////////////

include ('includes/login_page.inc.php');
if (isset($_POST['submitted']))
{
   //FOR THE LOCAL SERVER
   //$_SESSION['loginstring']='connect_local.php';
   //$_SESSION['login2string']='connect_local2.php';
   //$_SESSION['login2patientsatisfaction']='localconnectpatsat.php';
   
   //FOR THE WEB SITE
   $_SESSION['loginstring']='connect.php';
   $_SESSION['login2string']='connect2.php';
   $_SESSION['username'] = 'PAAPA';
   
   require_once ('includes/login_functions.inc.php');
   
   require_once ($_SESSION['loginstring']);
   
   list ($check, $data) = check_login($dbc, $_POST['initials'], $_POST['pass']);
   
   /*
   echo $check;
   echo '<br>';
   echo $data['initials'];
   echo '<br>';
   echo $data['pass'];
    * 
    */

   if ($check)
   {
      //$cookie_name = "user";
      //$cookie_value = $data['initials'];
      //$cookie_name = "pass";
      //$cookie_value = $data['pass'];
      $_SESSION['initials'] = $data['initials'];
      $_SESSION['pass'] = $data['pass'];
      mysqli_close($dbc);
      header('Location: choose1_02_01.php');
      die;
   }
   else
   {
      $errors = $data;
      var_dump($errors);
   }
}
?>
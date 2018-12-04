<?php

/*
 * Version 01_01
 * Page to delete a user.
 *
 * Last Revised:  2015-07-03
 */
 
session_start();
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="style.css" type="text/css">
';
echo '<TITLE>Delete User</TITLE>';




/*
 * Check to see if the user is logged in.
 * If not send them to the login page.
 */
if (!isset($_SESSION['initials']))
{
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}
else
{
    include ('includes/header.php');
    
    $q = "SELECT first, last ".
         "FROM mds ".
         "WHERE number = {$_POST['usernumber']}";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    echo '<center><h2>'.$a['first'].' '.$a['last'].' has been deleted.</center></h2><br><br>';
    
    $q = "DELETE FROM mds WHERE number = {$_POST['usernumber']}";
    $r = mysql_query($q);
        
    include ('includes/startover.html');
    include ('includes/logoutDirect.php');
}
?>

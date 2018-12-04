<?php

/*
 * Version 01_01
 * Page to determine access of the user and send them to the correct user page.
 *
 * Last Revised:  2015-07-03
 */
 
session_start();
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="styleP.css" type="text/css">
';
echo '<TITLE>Modify User</TITLE>';




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

    $q =   "UPDATE  mds ".
           "SET ".
           "first = '{$_POST['firstName']}', ".
           "last =  '{$_POST['lastName']}', ".
           "access = {$_POST['access']}, ".
           "payfraction = {$_POST['payfraction']}, ".
           "peds = {$_POST['peds']}, ".
           "admin = {$_POST['administrative']}, ".
           "business = {$_POST['business']} ".
           "WHERE number = {$_SESSION['usernumber']}";

    if(mysql_query($q))
    {
        echo '<center><h2>'.
                $_POST['firstName'].' '.$_POST['lastName'].
                ' information has been updated into the User database.
                </center></h2><br><br>';
    }
    else
    {
        echo '<center><h2>
            The user was not entered correctly.</h2></center><br>';
    }
    include ('includes/startover.html');
    include ('includes/logoutDirect.php');
}

?>

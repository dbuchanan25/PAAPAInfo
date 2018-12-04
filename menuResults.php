<?php

session_start();

echo'
<link rel="stylesheet" href="style.css" type="text/css">';


if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}

/*
 * If the user is logged-on correctly, the program comes here.
 */
else
{
    if ($_REQUEST['Me2'] == 'Main Menu') {
        header("Location: choose1_02_01.php");
    }
    else if ($_REQUEST['Me2'] == 'Schedule For Page') {
        header("Location: choose.php");
    }
    else if ($_REQUEST['Me2'] == 'Complete Month') {
        header("Location: monthcalendar2.php");
    }
    else if ($_REQUEST['Me2'] == 'ORMGR Worksheet') {
        header("Location: ormpre.php");
    }
    else if ($_REQUEST['Me2'] == 'Meeting Notification') {
        header("Location: meetingnotifi.php");
    }
    else if ($_REQUEST['Me2'] == 'Unscheduled Vacation List') {
        header("Location: unwantedlist.php");
    }
    else if ($_REQUEST['Me2'] == 'Advance Two Days') {
        header("Location: advanceone.php");
    }
    else if ($_REQUEST['Me2'] == 'Print Friendly Version') {
        header("Location: printfriendly.php");
    }
    else if ($_REQUEST['Me2'] == 'Help') {
        header("Location: Instructions.php");
    }
    else if ($_REQUEST['Me2'] == 'Assign Unscheduled Vacation') {
        header("Location: unwanted.php");
    }
    else if ($_REQUEST['Me2'] == 'Logout') {
        header("Location: logout.php");
    }
    else if ($_REQUEST['Me2'] == 'ORMAssignment') {
        header("Location: ormassignmentpre.php");
    }
    else if ($_REQUEST['Me2'] == 'ORMAssignments') {
        header("Location: ormassignmentspre.php");
    }
}
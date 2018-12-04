<?php
//session_start();
$a = session_id();
if(empty($a)) session_start();

/*
 * VERSION 02_01
 */
/*
 * Last Revised 2015-01-04
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
    echo '<title>Instructions</title>';
    //including the file menuBar.php to standardize the menu bar across the top
    //of the page
    include ('menuBar.php');

    require_once ($_SESSION['login2string']);

    include ('includes/header2.php');
    /*
    * Place the menu bar
    */    
    menuBar(1087);

    echo'
    <h1><center>Video Describing the Proper Method Getting Credit for Working After Call<center></h1>
    <br><br>
    <center><video height="600" width="1058" preload="auto" controls="controls" align="center">
        <source src="WorkingAfterCall.mp4" type="video/mp4">
        Your browser does not support this video.  Please use a newer browser.
    </video>
    <br><br>
    <br><br>
    <br>
    
    <h1><center>Click on the link below to download the PAMS Instruction Manual in .pdf form</center></h1>
    <br><br>


    <center><a align="center" href="PAMS Instructions - Complete.pdf" target="_blank">Instructions (.pdf)</a>';

   
}
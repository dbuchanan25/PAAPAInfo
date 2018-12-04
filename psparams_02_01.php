<?php
//session_start();
$a = session_id();
if(empty($a)) session_start();


////////////////////////////////////////////////////////////////////////////////
//VERSION 03_01                                                               //
//LAST REVISED 20140722 - Revised to include only aging for privacy concerns  //
//REVISED 201105011451                                                        //
//This file is for use with version 02_01 revised 201105011451                //
//Revised 201105171204 to include more locations.                             //
////////////////////////////////////////////////////////////////////////////////

echo'
<link rel="stylesheet" href="style.css" type="text/css">
';

if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}
else
{    
    if ($_POST['PAMS']==='PAMS')
    {
        $page_title = "PAMS";
        include "choose.php";
    }
    
    else if ($_POST['PAMS']==='Partner Management') {
        include "partners.php";
    }
    
    else if ($_POST['PAMS']==='Pay Report') {
        include "payreport.php";
    }
        
         
    else if ($_POST['PAMS']==='Patient Satisfaction Survey')
    {
        include "transferpatsat.php";
        //include "https://www.tascheter.com/patsat/paaentry.php";
    }			
}
?>
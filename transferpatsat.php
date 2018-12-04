<?php
$a = session_id();
if(empty($a)) session_start();
//Constructed 20170424 to transfer PAMS to Tascheter
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
    $page_title = "Patient Satisfaction Results";
    
    echo '<html>
          <head>';
    include ('includes/header.php');
   
    
   $a = $_SESSION['pass'];
   $l = strlen($a);
   if ($l < 40)
       str_pad($a, 40, '0', STR_PAD_LEFT);
   
   $a1 = (base_convert(substr($a, 0, 10), 16, 32));
   $a2 = (base_convert(substr($a, 10, 10), 16, 32));
   $a3 = (base_convert(substr($a, 20, 10), 16, 32));
   $a4 = (base_convert(substr($a, 30, 10), 16, 32));

   $z = $a1.'*'.$a2.'*'.$a3.'*'.$a4.'*'.strtolower($_SESSION['initials']);
   
   echo'
    <center>
    <h1>You are being transferred to the TASCHETER.COM website<br>
        to view patient satisfaction results.<br><br>
        You will be automatically logged into the TASCHETER site.<br><br>                
        You will be logged out of the PAMS website.<br>    
    <META HTTP-EQUIV="refresh" CONTENT="7;URL=https://www.tascheter.com/patsat/paaentry.php?paa='.$z.'">';
   
   
   //<META HTTP-EQUIV="refresh" CONTENT="7;URL=https://www.tascheter.com/patsat/paaentry.php?paa='.$z.'">';
   //http://www.internetofficer.com/seo/html-redirect/
   
   /*
    * //////////////////////////////////////////////////////////////////////////////////
   <script type="text/javascript">
    <!--
    function delayer(){
        window.location = "https://www.tascheter.com/patsat/paaentry.php?<?php'.$z.'?>"
    }
    //-->
   </script>
    </head>
        <body onLoad="setTimeout('delayer()', 7000)>"           
    *//////////////////////////////////////////////////////////////////////////////////// 
   //http://tizag.com/javascriptT/javascriptredirect.php

    echo'
    </h1>
    <img src=Tascheter_Logo.png width="280" height="125" title="TASCHETER" alt="Tascheter Logo" /> 
    </body>
    </html>';
    $_SESSION = array();
    session_destroy();
    setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0);
} 
?>
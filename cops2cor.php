<?php
session_start();


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
   require_once ($_SESSION['login2string']);


   echo '<br><br>';

   $sqlpass = "SELECT mdnumber, daynumber
               FROM monthassignment 
               WHERE assignment LIKE 'C OR%' AND 
               monthnumber = 7 AND yearnumber = 2014 AND weekend = 0 AND 
               daynumber < 26 ORDER BY daynumber";
   $sqlpassq = mysql_query($sqlpass);
   while ($sqlpassr = mysql_fetch_row($sqlpassq))
   {
       $sqlpass2 = "SELECT *
                   FROM monthassignment 
                   WHERE assignment LIKE 'COPS2' AND 
                   monthnumber = 7 AND yearnumber = 2014 AND weekend = 0 AND 
                   daynumber = {$sqlpassr[1]} AND mdnumber = {$sqlpassr[0]}";
       $sqlpassr2 = mysql_query($sqlpass2);
       if (mysql_num_rows($sqlpassr2) == 1)
       {
           $sqldel = "DELETE FROM monthassignment WHERE mdnumber = {$sqlpassr[0]} AND
                      monthnumber = 7 AND yearnumber = 2014 AND daynumber = {$sqlpassr[1]}";
           echo $sqldel.'<br>';
           mysql_query($sqldel);
           $sqldel = "DELETE FROM monthcal WHERE mdnumber = {$sqlpassr[0]} AND
                      monthnumber = 7 AND yearnumber = 2014 AND daynumber = {$sqlpassr[1]}";
           echo $sqldel.'<br>';
           mysql_query($sqldel);
           $sqladd = "INSERT INTO monthassignment VALUES ({$sqlpassr[0]}, 7, {$sqlpassr[1]},
                      2014, 'COPS2/C OR', 1, '06:30', 2, '14:30', 34, 0, now(), NULL, NULL)";
           echo $sqladd.'<br>';
           mysql_query($sqladd);
           $sqladd = "INSERT INTO monthassignment VALUES ({$sqlpassr[0]}, 7, {$sqlpassr[1]},
                      2014, 'COPS2/C OR', 3, '14:30', 34, '06:30', 98, 0, now(), NULL, NULL)";
           echo $sqladd.'<br>';
           mysql_query($sqladd);
           $sqladd = "INSERT INTO monthcal VALUES ({$sqlpassr[0]}, 7, {$sqlpassr[1]},
                      2014, 'COPS2/C OR', 1, 0, '06:30', '14:30')";
           echo $sqladd.'<br>';
           mysql_query($sqladd);
           $sqladd = "INSERT INTO monthcal VALUES ({$sqlpassr[0]}, 7, {$sqlpassr[1]},
                      2014, 'COPS2/C OR', 3, 0, '14:30', '06:30')";
           echo $sqladd.'<br>';
           mysql_query($sqladd);
       }         
   }
   echo '<h1>FINISHED</h1>';
   

}
?>

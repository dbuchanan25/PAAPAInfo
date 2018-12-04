<?php
session_start();

/*
 * Version 02_02
 */
/*
 * Last Revised: 2014-02-13
 * Revised: 2014-01-17
 * Revised: 2011-06-12
 */

/*
 * Revised 2014-02-13 to fix a bug in the insertion statement for monthcal
 * Revised 2014-01-17 to record insert statements into log.txt
 */

/*
  $dimo                   = days in month
  $dty                    = current year
  $mno                    = month (numerical) ie January=1
  $mn                     = month (alphabetical)
  $frow                   = user's name (first & last)
  $firstdayofweek         = Monday=1...Sunday=7 for the selected month
  $formd                  = initials for the schedule for physician
  $maa[]                  = array with monthly assignments
  
  $_SESSION['schedmd']    = initials for the "schedule for" physician
  $_SESSION['initials']   = initials for the "using" physician
  $_SESSION['schedmdnum'] = md number for the "schedule for" physician
  $_SESSION['dty']        = year
  $_SESSION['dtm']        = month (numerical)
  $_SESSION['mn']         = month (alphabetical)
*/


/*Check to see is the user is logged in*/
if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}
else
{
   $page_title = 'Choose Day';
   require_once ($_SESSION['login2string']);
   include ('includes/header.php');
   echo'
        <body><center>
	<div class="menu">
	<table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#E5E5E5"
         height="40px">
            <tr align="center">
                <td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="choose.php">Schedule For Page</a></td>
		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="monthcalendar2.php">Complete Month</a></td>
		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="ormpage.php">ORMGR Worksheet</a></td>
                <td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="unwanted.php">Assign Unwanted Vacation</a></td>
   		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="logout.php">Logout</a></td>
            </tr>
        </table>
        </div>';
   
   $mdindexx = $_REQUEST['umd'];
   
   $ss = "SELECT * FROM unwantedvac
          WHERE indexx = {$mdindexx}";
   $qq = mysql_query($ss);
   $aa = mysql_fetch_row($qq);
   $md = $aa[0];
   
   $s = "DELETE FROM monthassignment
         WHERE mdnumber = {$md}
         AND yearnumber = {$_SESSION['unwantyear']}
         AND monthnumber = {$_SESSION['unwantmonth']}
         AND daynumber = {$_SESSION['unwantday']}
         AND beginblock < 10";
   mysql_query($s);
   
   $s1 = "DELETE FROM monthcal
          WHERE mdnumber = {$md}
          AND yearnumber = {$_SESSION['unwantyear']}
          AND monthnumber = {$_SESSION['unwantmonth']}
          AND daynumber = {$_SESSION['unwantday']}
          AND begintime < '09:00:00'";
   mysql_query($s1);
   
   $s2 = "INSERT INTO monthassignment VALUES ".
          "({$md}, {$_SESSION['unwantmonth']}, {$_SESSION['unwantday']}, {$_SESSION['unwantyear']},".
          " 'Unwanted Vac', 1, '06:00:00', 0, '06:00:00', 96, 0, now(),". 
          " '{$_SESSION['initials']}', NULL)";
    
    $istring = str_replace("'","",$s2);       
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'unwanted3.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);      
           
   $file = fopen("log.txt", "a") or exit("Unable to open file!");
   $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
   $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                   "User: {$_SESSION['initials']}\n". 
                   "Page: unwanted3.php\n".
                   "Statement: {$s2}\n\n";
   fwrite($file, $logStatement);
   fclose($file);        
           
   mysql_query($s2);
   
   $s3 = "INSERT INTO monthcal VALUES
          ({$md}, {$_SESSION['unwantmonth']}, {$_SESSION['unwantday']}, {$_SESSION['unwantyear']},
           'Unwanted Vac', 1, 0, '06:00:00', '06:00:00')";
          
    $istring = str_replace("'","",$s3);       
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'unwanted3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
    
   mysql_query($s3);
   
   $s4 = "DELETE FROM unwantedvac
          WHERE indexx = {$mdindexx}";
   mysql_query($s4);
   
   $s5 = "SELECT first,last FROM mds
          WHERE number = {$md}";
   $q5 = mysql_query($s5);
   $a5 = mysql_fetch_row($q5);
   $first = $a5[0];
   $last = $a5[1];
   
   $s6 = "INSERT INTO unwantedvacpast VALUES
          ({$md}, {$mdindexx}, {$_SESSION['unwantday']}, 
           {$_SESSION['unwantmonth']}, {$_SESSION['unwantyear']}, NULL)";
   echo $s6;
   mysql_query($s6);
   
   
   echo' <br><br><h2><center>Assignment of Unwanted Vacation Made For:</center></h2><br>';
   echo' <h2><center>'.$first.' '.$last.'</center></h2>';
   echo' <h2><center>for '.$_SESSION['unwantmonth'].'/'.$_SESSION['unwantday'].
           '/'.$_SESSION['unwantyear'].'</center></h2><br><br>';
   
   include ('includes/footer.html');
}
?>

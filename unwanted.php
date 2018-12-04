<?php
session_start();

/*
 * Version 02_02
 */
/*
 * Last Revised: 2014-01-18
 * Revised: 2011-06-12
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
    
   $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'unwanted.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
    
    
   $page_title = 'Choose Unwanted Day';
   echo '<title>'.$page_title.'</title>';
   
   require_once ($_SESSION['login2string']);
   include ('includes/header.php');
   include ('checkHolidayDayOfYear.php');
   
   $s0 = "SELECT number
          FROM mds
          WHERE initials = '{$_SESSION['initials']}'";
   $q0 = mysql_query($s0);
   $a0 = mysql_fetch_row($q0);
   $mdno = $a0[0];
   
   $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
   $dyear = $datetime->format('Y');
   $dm = $datetime->format('n');
   $dd = $datetime->format('j');
   
   $s1 = "SELECT assignment 
          FROM monthassignment
          WHERE mdnumber = {$mdno}
          AND monthnumber = {$dm}
          AND daynumber = {$dd}
          AND yearnumber = {$dyear}";
   $q1 = mysql_query($s1);
   $a1 = mysql_fetch_row($q1);
   $assig = trim($a1[0]);
   
   require_once 'menuBar.php';
   menuBar(223);
   
   
   /*
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
    * 
    */
   
   if ($assig == 'ORMGR' || $assig == 'ORMGR/Peds' || 
           $_SESSION['initials']=='DB' || $_SESSION['initials']=='PS')
   {
   $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
   echo "<br><br><h2><center>Choose Unwanted Vacation Assignment Date: </center></h2><br><br>";
   
   echo'<form method="post" action="unwanted2.php">';
   echo'
		<table align="center" class="content" border="0"
                    width="100%" bordercolor="#000000">
                    <tr>
			<td width="40%"></td>
			<td width="20%" height="40px" align="center"> Choose Date: </td>
			<td width="40%"></td>
                    </tr>';
   
   echo '           <tr>';
   echo'
		      <td width="40%"></td>
		      <td width="20%" height="40px" align="center">
                      <select name="udate">';
   
   for ($x=0; $x<6; $x++)
   {
    echo'
            <option value='.$x.'>';
                
            if ($x == 0)
            {
                echo 'TODAY, ';
            }
            
            echo              
            $datetime->format('l').', '.
            $datetime->format('F').', '.
            $datetime->format('j').'</option>\n';
            $datetime->modify("+1 day");
   }
   
   echo '             </select></td>';   
   echo '<td width="40%"></td></tr>';

   echo '<tr>
         </tr>
         <tr>
               <td height="40px" >
               </td>
               <td align="center" style="border-style:solid;border-color:#D7DAE1;
               background-color:#D7DAE1">
               <input type="submit" name="submit" value="Submit" 
                       class="btn">
               </td>
               </tr>';
   echo '</table></div></form><br><br>';
   $datetime->modify("-6 days");
   }
   else
   {
       echo '<br><br><h2><center>You need to have the ORMGR make these changes.
             </center></h2><br><br>';
   }         
   include ('includes/footer.html');
}
?>

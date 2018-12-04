<?php

/*
   This displays the COMPLETE MONTH page.
   It calls "monthcalendar2.php" to help display the page.
*/

session_start();

if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}

/*
   If the user is logged in then the program skips to here.
*/
/*
   ---VARIABLES---
   $dimo	   	  		 :  	  Contains the number of days in the month being displayed
   $_SESSION['dimo']	 :		  Session variable containing the same information
   $_SESSION['dtm']		 :		  The number of this month
   $_SESSION['dty']		 :		  The number of this year
   $_SESSION['schedmd']	 :		  Session variable containing the initials of the physician of the schedule
   $formd				 :		  Local variable containing the physician information (initials)
   $schedmdnumber		 :		  Result from query containing the number of the $formd
   $_SESSION['schedmdnum']:		  Session variable containing the number of the physician
   $for_row				 :		  First and Last name of the physician from $formd
   $_SESSION['initials'] :		  Session variable containing the initials of the user physician 
								  (the physician who is logged on)
   $frow				 :		  Contains the first and last names of the user physician
   $sqlmdq		  		 :		  Is an array which contains the number and last name of all physicians
   						 		  ordered by number.
   $sqlmdf			     :		  Contains each row of $sqlmdq
*/
else
{
   $page_title = 'Month Schedule';
   require_once ('../connect2.php');
   include ('includes/header.php');
   include ('monthcalendar2.php');

  $dimo = cal_days_in_month(CAL_GREGORIAN, $_SESSION['dtm'], $_SESSION['dty']) ;
  $_SESSION['dimo']=$dimo;

  
  $formd = $_SESSION['schedmd'];
  
  $r = "select number from mds where initials='$formd'";
  $r1 = mysql_query($r);
  $schedmdnumber = mysql_fetch_row($r1);
  $_SESSION['schedmdnum']=$schedmdnumber[0];
  
  $fqu = "Select first, last from mds where initials='$formd'";
  $forfirstlast = mysql_query($fqu);
  $for_row = mysql_fetch_row($forfirstlast);
  
  $test = $_SESSION['initials'];
  $qu = "Select first, last from mds where initials='$test'";
  $firstlast = mysql_query($qu);
  $frow = mysql_fetch_row($firstlast);
  
  
  
  
  echo'
	<body><center>
		
		<div class="menu">
		<table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#E5E5E5">
		<tr align="center">
		    <td align="center" width="225px" height="25px" style="color:black" bordercolor="#808080">User: '.$frow[0].' '.$frow[1].'</td>
			<td align="center" width="150px" height="25px" bordercolor="#808080"><a href="choose.php">Schedule For Page</a></td>
			<td align="center" width="150px" height="25px" bordercolor="#808080"><a href="ormpage.php">ORMGR Worksheet</a></td>	
   			<td align="center" width="100px" height="25px" bordercolor="#808080"><a href="login.php">Logout</a></td>
			
		</tr>
		</table>
		</div><br><br>';
		 
  

  echo "<table><tr><td><center><h3>Schedule For: {$_SESSION['dtm']}/{$_SESSION['dty']}</td>";
  echo '</tr></table>';
 
  echo '<br><br>';

  $sqlmd = "SELECT DISTINCT number, last FROM mds order by number";
  $sqlmdq = mysql_query($sqlmd);
  
  echo '<table border="1" style="width:3200px">';
  echo '   <tr>
              <td style="width:100px">
		      </td>';
			  
  for ($xd=1; $xd<=$dimo; $xd++)
  {
     echo '   <td style="width:100px">
	          '.$xd.'
		      </td>';
  }
  
  echo '   </tr>';
  
  while ($sqlmdf = mysql_fetch_row($sqlmdq))
  {
     echo '<tr>
              <td bgcolor="white" width="100" height="50" align="center">
                 <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
		               <td height="20" bgcolor="white" width="98" align="center" style="font-size:medium;">
					   <b>';
	echo               $sqlmdf[1];
	echo '             </b>
	                   </td>
					</tr>
					<tr>
					</tr>
					<tr>
	                   <td height="20" bgcolor="white" width="98" align="center" style="font-size:medium;">';
	echo               $sqlmdf[0]; 
	echo '             </td>
	                </tr>
                 </table>
              </td>';
		
			  
	/*
	   The function "monthcalendar2" is called.  It is given the
	   number of the physician for that row of the month calendar
	   being constructed.
	*/
	
	monthcalendar2 ($sqlmdf[0]);
	
    echo ' </tr>';
  }
  echo '</table>';

  echo '<br><br>';   
include ('includes/footer.html');
}

?>

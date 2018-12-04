<?php
if (!isset($_SESSION)) { session_start(); }
require_once ('connect2.php');
include ('includes/header.php');

/*
  $dimo                = days in month
  $dty                 = current year
  $mno                 = month (numerical) ie January=1
  $mn                  = month (alphabetical)
  $frow                = user's name (first & last)
  $firstdayofweek      = Monday=1...Sunday=7 for the selected month
  $formd               = initials for the schedule for physician
  $maa[]               = array with monthly assignments
  
  $_SESSION['schedmd'] = initials for the "schedule for" physician
  $_SESSION['initials']= initials for the "using" physician
  $_SESSION['schedmdnum'] = md number for the "schedule for" physician
  $_SESSION['dty']     = year
  $_SESSION['dtm']     = month (numerical)
  $_SESSION['mn']      = month (alphabetical)
*/

/*Check to see is the user is logged in*/
if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}


else if ($_POST['PAMS'])
{
	
	   $ini = $_SESSION['initials'];
	   echo'<form method="post" action="choose2.php">
	        <div class="content">
			<table align="center" class="content" border="0" width="1250" bordercolor="#000000">
	        <tr>
			<td width="250"></td>
			<td width="250" align="center"> Choose Anesthesiologist: </td>
			<td width="250"></td>
			<td width="250" align="center">Choose Month: </td>
			<td width="250"></td>
			</tr>';
			 
		$result=mysql_query('Select first, last, initials from mds order by last');
		
		echo '<tr>
		      <td width="100"></td>
		      <td width="250" align="center"><select name="nameinitials">';
		while($row = mysql_fetch_row($result))
		{  
		  if ($row[2]==$ini)
		  {
		    echo "<option selected='selected' value=$row[2]>$row[0] $row[1]</option>\n";
		  }
		  else
		  {
		  	echo "<option value=$row[2]>$row[0] $row[1]</option>\n";
		  }  
		}
		  echo '</select></td><td></td>';
		  
		
		$datetime = new DateTime('today');
		
		$_SESSION['dty']=$datetime->format('Y');
		
		$_SESSION['dtd']=$datetime->format('j');
		
		$dyear = $datetime->format('Y');
		$dy = $datetime->format('d');
		$dm = $datetime->format('n');
		
		if ($ini=='DB' || $ini=='JB')
		{
		   if ($dy<6)
		   {
		     $datetime->modify("-2 weeks");
			 $datetime->modify("-1 months");
		   }
		   else
		   {
		     $datetime->modify("-2 months");
		   }
		   echo '<td><center><select name="mn"><option>'.$datetime->format('F').'</option>\n';
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   if ($dy<6)
		   {
		     $datetime->modify("-2 weeks");
		   }
		   else
		   {
		     $datetime->modify("-1 months");
		   }
		   echo '<option>'.$datetime->format('F').'</option>\n';
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   echo '<option selected="selected">'.$datetime->format('F').'</option>\n';
		   
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   echo '<option>'.$datetime->format('F').'</option>';
		}
		
		else if ($dy<6)
		{
		   $datetime->modify("-2 weeks");
		   echo '<td><center><select name="mn"><option>'.$datetime->format('F').'</option>\n';
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   echo '<option selected="selected">'.$datetime->format('F').'</option>\n';
		   
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   echo '<option>'.$datetime->format('F').'</option>';
		}
		
		else
		{  
		   echo '<td><center><select name="mn"><option selected="selected">'.$datetime->format('F').'</option>\n';
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   echo '<option>'.$datetime->format('F').'</option>';
		}
		echo '</td></tr>';
		echo '<tr>
		      </tr>
		      <tr>
			  <td>
			  </td>
			  <td>
			  </td>
			  <td align="center"><input type="submit" name="submit" value="Submit" style="width:200px; height:30px">
			  </td>
			  </tr>';
		echo '</table><br><br>';
	/*
	else
	{
	   $page_title = 'Choose Day';
	   require_once ('connect2.php');
	   include ('includes/header.html');
	   include ('daycalendar.php');
	
	  $dty = $_SESSION['dty'];
	
	  switch ($_REQUEST['mn'])
	  {
	     case 'January':
		    $mno = 1;
			break;
		 case 'February':
		    $mno = 2;
			break;
		 case 'March':
		    $mno = 3;
			break;
		 case 'April':
		    $mno = 4;
			break;
		 case 'May':
		    $mno = 5;
			break;
		 case 'June':
		    $mno = 6;
			break;
		 case 'July':
		    $mno = 7;
			break;
		 case 'August':
		    $mno = 8;
			break;
		 case 'September':
		    $mno = 9;
			break;
		 case 'October':
		    $mno = 10;
			break;
		 case 'November':
		    $mno = 11;
			break;
		 case 'December':
		    $mno = 12;
			break;
	   }
		  $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
	  
	  if (
	       ($datetime->format('m')==1 && ($mno==12 || $mno==11 || $mno==10))
		   ||
		   ($datetime->format('m')==2 && ($mno==12 || $mno==11))
		   ||
		   ($datetime->format('m')==3 && ($mno==12))
		 )
	  {
	     $dty--;
		 $_SESSION['dty']--;
	  }
	  else if ($datetime->format('m')==12 && $mno==1)
	  {
	     $dty++;
		 $_SESSION['dty']++;
	  }
	  $datetime->setDate($dty,$mno,1);
	  $dimo = cal_days_in_month(CAL_GREGORIAN, $mno, $dty) ;
	  $firstdayofweek = $datetime->format('N');
	  $_SESSION['dtm']=$mno;
	  $_SESSION['mn']=$_REQUEST['mn'];
	  $_SESSION['schedmd']=$_REQUEST['nameinitials'];
	  
	  
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
	  
	  $numericaldayofmonth=0;
	  
	  $domo = $numericaldayofmonth+1;
	  $monthassignpri = "select assignment, beginblock, endblock, weekend from monthassignment where daynumber=$domo and yearnumber={$_SESSION['dty']} and monthnumber={$_SESSION['dtm']} and assigntype=1 and mdnumber={$_SESSION['schedmdnum']}";
	  $rpri2 = mysql_query($monthassignpri);
	  if (!mysql_fetch_array($rpri2))
	  {
	     echo'<form method="post" action="choose.php">';
		 echo "<table align='center' border='0' width='750' bordercolor='#000000'>
		       <tr>
			   <td align='center'>
			   <h2>That month's schedule is not yet available.</h2>
			   </td>
			   </tr>";
	     
		 echo '
	           <tr style="height:50"><td height="25"></td></tr>
	           <tr style="height:50">
			   <td align="center">
	           <input type="submit" name="submit" value="Submit" style="width:200px; height:30px">
		       </td>
			   </table>
			   </form>';
	  }
	  else
	  { 
	  echo'
		<body><center>
			
			<div class="menu">
			<table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#E5E5E5">
			<tr align="center">
			    <td align="center" width="225px" height="25px" style="color:black" bordercolor="#808080">User: '.$frow[0].' '.$frow[1].'</td>
				<td align="center" width="150px" height="25px" bordercolor="#808080"><a href="choose.php">Schedule For Page</a></td>
				<td align="center" width="150px" height="25px" bordercolor="#808080"><a href="monthcalendar2.php">Complete Month</a></td>
				<td align="center" width="150px" height="25px" bordercolor="#808080"><a href="ormpage.php">ORMGR Worksheet</a></td>
	   			<td align="center" width="100px" height="25px" bordercolor="#808080"><a href="login.php">Logout</a></td>
				
			</tr>
			</table>
			</div><br><br>';
	  
	  echo'<form method="post" action="day_display.php">
	         <div class="content"><table align="center" class="content" border="0" width="750" bordercolor="#000000">
	         <tr>
			 <td width="250"><center> Schedule For: </td>
			 <td width="250"><center> Month: </td>
			 <td width="250"><center> Day: </td>
			 </tr>';
			 
	  
	
	  echo "<td align='center'><h4>$for_row[0] $for_row[1]</td><td><center><h4>{$_REQUEST['mn']}</td><td><center><select name='dai'>";
	  $x = 0;
	  for ($x=1; $x<=$dimo; $x++)
	  {
	     echo "<option>$x</option>\n";
	  }
	  
	 echo '</select></td></tr></table>';
	 
	 echo '<br><br>';
	 echo '<table align="center" class="content" border="0" width="750" bordercolor="#000000">
	       <tr></tr>
	       <tr><td></td><td align="center">
	       <input type="submit" name="submit" value="Submit" style="width:200px; height:30px">
		   </td></table>';
	
	 echo'
	    <br><br>
	    <table border="0" cellspacing="1">
	    <tr>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Sun
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Mon
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Tue
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Wed
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Thu
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Fri
			   </td>
	          </tr>
			</table>
		 </td>
		 <td bgcolor="#000000" width="150" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="18" width="146" align="center" style="font-size:small">
			   Sat
			   </td>
	          </tr>
			</table>
		 </td>
		 </tr>';
		 
	
	 
	   switch ($firstdayofweek)
	   {
	     case '1':
	     echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>';
		
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
		 
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   echo'
		</tr>
		<tr>'; 
	    break;
		
		
	    case '2':
		 echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;   
	   
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '3':
		 echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
	
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '4':
		 echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   echo' 
	    </tr> 
		<tr>';
		break;  
		
		case '5':
		 echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	     
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '6':
		 echo'
		 <tr>  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
	      
		 <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   echo'
		</tr>
		<tr>';
		break;
		
		case '7':
	     echo'
		 <tr>'; 
		 
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	   
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++; 
		 
		 echo'
		 </tr>
		 <tr>'; 
	     break;
		 };
		 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//From here is the middle of the calendar after the first day is situated.                                 //
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 echo'  
		</tr><tr>';
		
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		
	    echo'
		</tr><tr>';	
		
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		 
		 daycalendar($numericaldayofmonth);
		 $numericaldayofmonth++;
		
	     echo'
		 </tr>';
		
	if ($numericaldayofmonth<$dimo)
	{
	   echo '
	   <tr>';
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	if ($numericaldayofmonth<$dimo)
	{
	   echo '</tr><tr>';
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	
	else
	{
	    echo'
		<tr>
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   daycalendar($numericaldayofmonth);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="#000000" width="150" height="150" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="130" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 </tr>
		 ';
	}
	
		
		
	echo'
	    </table>
	    <br>
	    <br>
	 ';
	$_SESSION['schedchange']=0;
	}
	
*/		   
	include ('includes/footer.html');
}

else 
{

	echo '<center><h2>Vacation Functions and Database Management</center></h2><br><br>
	      <body onLoad="document.name.QUICKENTRY.focus()">';
		
		   echo '
		         <form method="post" action="choose2C.php" class="input" name="name">		   
		         <table align="center" width="100%">
		         <tr>
		         <td align="center">
				 <input type="submit" name="ENTRY" value="Vacation Functions" class="btn">
				 </td>
				 </tr>
				 <tr>
				 <td height="25">
				 </td>
				 </tr>
				 <tr>
		         <td align="center">
				 <input type="submit" name="QUICKENTRY" value="Quick Entry" class="btn">
				 </td>
				 </tr>
				 <tr>
				 <td height="25">
				 </td>
				 </tr>
				 <tr>
				 <td align="center">
				 <input type="submit" name="DATABASE" value="Database Management" class="btn"> </form>
		         </td>
		         </tr>
		         <td height="25">
				 </td>
				 </tr>
				 <tr>
				 <td align="center">
				 <input type="submit" name="LOGOUT" value="Logout" class="btn"> 
		         </td>
		         </tr>
		         </table>
		         </form>		   
		         <br><br>';
}
?>
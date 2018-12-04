<?php
if (!isset($_SESSION)) { session_start(); }

echo'
<link rel="stylesheet" href="style.css" type="text/css">
';

/*
 * Version 02_02
 */
/*
 * Last Revised 2014-01-13
 * Revised 2011-06-12
 * previous page:  "choose.php"
 * next page:  "day_display.php" or any of the menu item pages
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

/*
 * If the user is logged-on correctly, the program comes here.
 */
else
{
    $page_title = 'Choose Day';
    echo '<title>'.$page_title.'</title>';

    require_once ($_SESSION['login2string']);
    include ('includes/header.php');
    
    //including the file menuBar.php to standardize the menu bar across the top
    //of the page
    require_once ('menuBar.php');

    /*
    * daycalendar.php is called to display the particulars of each day's
    * assignments
    */
    include ('daycalendar.php');

    $dty = $_SESSION['dty'];

    /*
    * get the month choosen from "choose.php" to display.
    */
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

    //IF A USER IS LOOKING AT A MONTH IN THE PREVIOUS YEAR OR IN THE NEXT YEAR THE VARIABLES
    //$dty AND $_SESSION['dty'] NEED TO BE CHANGE TO THE APPROPRIATE YEAR.
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

    /*
    * set the date to the first of the choosen month
    * get the number of days in the month
    * get what the first day of the week is
    */
    $datetime->setDate($dty,$mno,1);
    $dimo = cal_days_in_month(CAL_GREGORIAN, $mno, $dty) ;
    $firstdayofweek = $datetime->format('N');
    $_SESSION['dtm']=$mno;
    $_SESSION['mn']=$_REQUEST['mn'];
    $_SESSION['schedmd']=$_REQUEST['nameinitials'];

    /*
    * both the $_SESSION variable 'schedmd' and $formd
    * are the physician whose record is being accessed
    */
    $formd = $_SESSION['schedmd'];
    $_SESSION['spmd'] = $formd;

    /*
    * get the number of the $formd
    * get the first and last name of the $formd
    */
    $r = "SELECT number 
        FROM mds 
        WHERE initials='$formd'";
    $r1 = mysql_query($r);
    $schedmdnumber = mysql_fetch_row($r1);
    $_SESSION['schedmdnum']=$schedmdnumber[0];

    $fqu = "SELECT first, last 
          FROM mds 
          WHERE initials='$formd'";
    $forfirstlast = mysql_query($fqu);
    $for_row = mysql_fetch_row($forfirstlast);


    /*
    * $test gets the initials of the user, not necessarily the 
    * schedule being accessed
    * $_SESSION['initials'] are the initials of the user (logged-on)
    * physician
    */
    $test = $_SESSION['initials'];
    $qu = "SELECT first, last 
         FROM mds 
         WHERE initials='$test'";
    $firstlast = mysql_query($qu);
    $frow = mysql_fetch_row($firstlast);



    //CHECKING IF THERE IS A PRIMARY ASSIGNMENT FOR THE FIRST DAY OF THE CHOSEN MONTH IN THE 
    //DATABASE.  IF NOT THEN A MESSAGE DISPLAYING THE MONTH'S INFORMATION IS NOT AVAILABLE.
    $numericaldayofmonth=0;

    $domo = $numericaldayofmonth+1;
    $monthassignpri = "   SELECT assignment, beginblock, endblock, weekend
                        FROM monthassignment
                        WHERE daynumber=$domo
                        AND yearnumber={$_SESSION['dty']} 
                        AND monthnumber={$_SESSION['dtm']} 
                        AND assigntype=1 
                        AND mdnumber={$_SESSION['schedmdnum']}";
    $monthassignday = "   SELECT assignment, beginblock, endblock, weekend
                        FROM monthassignment
                        WHERE daynumber=2
                        AND yearnumber={$_SESSION['dty']}
                        AND monthnumber={$_SESSION['dtm']}
                        AND assigntype=1
                        AND mdnumber={$_SESSION['schedmdnum']}";
    $rpri3 = mysql_query($monthassignday);
    $rpri2 = mysql_query($monthassignpri);
    if (mysql_num_rows($rpri2)==0 && mysql_num_rows($rpri3)==0)
    {
         echo'<form method="post" action="choose.php">';
         echo "<table class=table5>
               <tr>
                   <td align='center'>
                   <h2>That month's schedule is not yet available.</h2>
                   </td>
                   </tr>";

         echo '
           <tr style="height:50"><td height="25"></td></tr>
           <tr style="height:50">
                   <td align="center">
           <input type="submit" name="submit" value="Submit" class="btn">
               </td>
                   </table>
                   </form>';
    }
  /*
   * If this month's schedule is available the program comes here.
   * The selected choice gets submitted to day_display.php
   */
  else
  { 
  
  /*
   * Place the menu bar
   */    
  menuBar(5663);
  
  
  echo'<form method="post" action="day_display.php">
       <div class="content">
       <table class="table5">
            <tr>
                <td width="250"><center> Schedule For: </td>
                <td width="250"><center> Month: </td>
                <td width="250"><center> Day: </td>
            </tr>';
		 


  echo "    <tr>
                <td>
                    <h4>$for_row[0] $for_row[1]
                </td>
                <td>
                <center>
                    <h4>{$_REQUEST['mn']}
                </td>
                <td>
                <center>
                <select name='dai'>";
  $x = 0;
  for ($x=1; $x<=$dimo; $x++)
  {
     echo "<option>$x</option>\n";
  }
  
 echo '         </select>
                </td>
            </tr>
        </table>';


 /*
  * The submit button
  */
 echo ' <br>
        <br>';
 echo ' <table class="table5">
            <tr>
                <td align="center">
               <input type="submit" name="submit" value="Submit" class="btn">
                </td>
            </tr>
        </table>';


 /*
  * The row which contains the names of the days of the week
  */
 echo'
        <br>
        <br>
        <table cellspacing="1" width="98%" align="center">
            <tr>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="1" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%"
                                            align="center" style="font-size:small">
                            Sun
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%" align="center"
                                                        style="font-size:small">
                            Mon
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%"
                                            align="center" style="font-size:small">
                            Tue
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%" align="center"
                                                        style="font-size:small">
                            Wed
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%"
                                            align="center" style="font-size:small">
                            Thu
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%" align="center"
                                                        style="font-size:small">
                            Fri
                            </td>
                        </tr>
                    </table>
                </td>
                <td bgcolor="#000000" width="14%" height="20" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="98%">
                        <tr>
                            <td bgcolor="#ffffff" height="18" width="90%" align="center"
                                                        style="font-size:small">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

         <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

	 <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

         <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
		   </td>
                </tr>
            </table>
	 </td>

         <td bgcolor="#ffffff" width="14%" height="150px" align="center">
            <table border="0" cellspacing="0" cellpadding="0" width="99%">
                <tr>
		   <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
		   </td>
                </tr>
                <tr>
		   <td bgcolor="#ffffff" height="130" width="100%">
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
	 
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//From here is the middle of the calendar after the first day is situated.                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
	 
	 
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
		   </td>
            </tr>
        </table>
    </td>
	 ';
}

//////////////////////////////////////////////////////////////////////////////////////////////////

if ($numericaldayofmonth<$dimo)
{
   echo '</tr><tr>';
   daycalendar($numericaldayofmonth);
   $numericaldayofmonth++;
}

else
{
    echo'
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
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
    <td bgcolor="#ffffff" width="14%" height="150px" align="center">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
		   <td bgcolor="#ffffff" height="20" width="14%" align="left" style="font-size:small">
		   </td>
            </tr>
            <tr>
		   <td bgcolor="#ffffff" height="130" width="14%">
		   </td>
            </tr>
        </table>
    </td>
	 ';
}

	
	
echo'
    </table>
    <br>
    <br>
 ';
$_SESSION['schedchange']=0;
}
	   
include ('includes/footer.html');
}
?>

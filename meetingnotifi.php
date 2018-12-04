<?php
if (!isset($_SESSION)) { session_start(); }
/*
 * Version 02_01
 */
/*
 * Last Revised: 2015-07-28
 * to include new menu style
 * Revised:  2014-01-13
 * previous page:  any of the menu containing pages
 * next page:  "meetingconfirm.php"
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
 * program comes here if a valid user is logged-in
 */
else
{
   if (!isset($_SESSION['schedmd'])) {
       $_SESSION['schedmd'] = $_SESSION['initials'];
   }
   $page_title = 'Meeting Notification';
   echo '<title>'.$page_title.'</title>';
   
   require_once ($_SESSION['login2string']);
   include ('includes/header.php');
   
   $test = $_SESSION['initials'];
   $qu = "SELECT first, last
          FROM mds
          WHERE initials='$test'";
   $firstlast = mysql_query($qu);
   $frow = mysql_fetch_row($firstlast);
   

/*
 * Display menu items
 */
echo'
<body>
        <center>';

include_once 'menuBar.php';
menuBar(1551);
echo'
       <br>
       <br>';

        echo'
            <h2><center>Enter Meeting Date and Time for ORMGR Notification for '.$_SESSION['initials'].'</center></h2>
            <br>
            <center>
            Enter the date, begin time, and end time of the meeting.
            <br>
            Then enter the reason for the meeting and any comments.
            </center>
            <br>';
        
        $datetime = new DateTime('today');
        $monthnm = $datetime->format('M');    //Jan Feb Mar...
        $yearnumber = $datetime->format('Y');


        echo'
            <form method="post" action="meetingconfirm.php">
            <div class="content">
            <table width="60%" align="center" border="1">
                <tr>
                    <td width="10%" align="center" bgcolor="#D7DAE1">Year:
                    </td>
                    <td width="20%" align="center"  style="color:black">';
        echo'       <select name = "yr">';
        
        if ($yearnumber == '2016')
        {
            echo ' <option selected value="2016">2016</option>';
        }
        else 
        {
            echo ' <option value="2016">2016</option>';
        }
        if ($yearnumber == '2017')
        {
            echo ' <option selected value="2017">2017</option>';
        }
        else 
        {
            echo ' <option value="2017">2017</option>';
        }
        if ($yearnumber == '2018')
        {
            echo ' <option selected value="2018">2018</option>';
        }
        else 
        {
            echo ' <option value="2018">2018</option>';
        }


                   
         echo'              
                    </select>      
                    <td width="10%" align="center" bgcolor="#D7DAE1">Month:
                    </td>
                    <td width="20%" align="center"  style="color:black">
                    <select name = "month">';
         
        
        if ($monthnm == 'Jan')
        {
            echo '<option selected value="1">January</option>';
        }
        else 
        {
            echo '<option value="1">January</option>';
        }
        if ($monthnm == 'Feb')
        {
            echo '<option selected value="2">February</option>';
        }
        else 
        {
            echo '<option value="2">February</option>';
        }
        if ($monthnm == 'Mar')
        {
            echo '<option selected value="3">March</option>';
        }
        else 
        {
            echo '<option value="3">March</option>';
        }
        if ($monthnm == 'Apr')
        {
            echo '<option selected value="4">April</option>';
        }
        else 
        {
            echo '<option value="4">April</option>';
        }
        if ($monthnm == 'May')
        {
            echo '<option selected value="5">May</option>';
        }
        else 
        {
            echo '<option value="5">May</option>';
        }
        if ($monthnm == 'Jun')
        {
            echo '<option selected value="6">June</option>';
        }
        else 
        {
            echo '<option value="6">June</option>';
        }
        if ($monthnm == 'Jul')
        {
            echo '<option selected value="7">July</option>';
        }
        else 
        {
            echo '<option value="7">July</option>';
        }
        if ($monthnm == 'Aug')
        {
            echo '<option selected value="8">August</option>';
        }
        else 
        {
            echo '<option value="8">August</option>';
        }
        if ($monthnm == 'Sep')
        {
            echo '<option selected value="9">September</option>';
        }
        else 
        {
            echo '<option value="9">September</option>';
        }
        if ($monthnm == 'Oct')
        {
            echo '<option selected value="10">October</option>';
        }
        else 
        {
            echo '<option value="10">October</option>';
        }
        if ($monthnm == 'Nov')
        {
            echo '<option selected value="11">November</option>';
        }
        else 
        {
            echo '<option value="11">November</option>';
        }
        if ($monthnm == 'Dec')
        {
            echo '<option selected value="12">December</option>';
        }
        else 
        {
            echo '<option value="12">December</option>';
        }
       

        echo'
                    </select>      
                    </td>
                    <td width="10%" align="center" bgcolor="#D7DAE1">Day:
                    </td>
                    <td width="20%" align="center" style="color:black">
                    <select name="dae">';
        
  for ($x=1; $x<=31; $x++)
  {
     echo "<option>$x</option>\n";
  }

  echo '            </select>
                    </td>';

  echo'
                </tr>
                </table>';
  
  echo'         <table width="60%" align="center" border="1">
                <tr height="15px">
                    <td></td>
                </tr>
                <tr>
                    <td width="25%" align="center" bgcolor="#D7DAE1">Begin Time:
                    </td>';
  
  $btstatement = "SELECT time, timeperiod
                  FROM timeperiods";
  $btquery = mysql_query($btstatement);
  
  $etstatement = "SELECT time, timeperiod
                  FROM timeperiods";
  $etquery = mysql_query($etstatement);
  
  echo'

                    <td width="25%" align="center">
                    <select name="bt">';
                    while ($bt = mysql_fetch_array($btquery))
                    {
                        $bttext = substr($bt[0],0,5);
                        echo "<option value=$bt[1]>$bttext</option>\n";
                    }
                    echo'
                    </select>
                    </td>

                    <td width="25%" align="center" bgcolor="#D7DAE1">Estimated End Time:
                    </td>
                    <td width="25%" align="center">
                    <select name="et">';
                    while ($et = mysql_fetch_array($etquery))
                    {
                        $ettext = substr($et[0],0,5);
                        echo "<option value=$et[1]>$ettext</option>\n";
                    }
                    echo'
                    </td>
                    </select>
                </tr>
                <tr height="15px">
                    <td></td>
                </tr>
             </table>
             <br><br>
             <table width="75%" style="border:1px solid black;">
                <tr>
                    <td width="20%" align="center" bgcolor="#D7DAE1">
                        Comments:
                    </td>
                    <td width="80%" align="left" style="border:1px solid black;">
                    <input type="text" size="250" name="commnt"
                        value="Enter Meeting Reason & Comments Here.">
                    </td>
                </tr>
             </table>';
echo ' <br>
        <br>';
 echo ' <table align="center" class="content" width="20%" bordercolor="#000000">
            <tr>
            </tr>
            <tr>
                <td>
                </td>
                <td align="center">
               <input type="submit" name="submit" value="Submit"
                    style="width:200px; height:40px; background-color:D7DAE1">
                </td>
            </tr>
        </table>
        </div>
        </form>
        <br><br><br>';
 
 
 echo '<center><b><h2>SCHEDULED AND RECENT MEETINGS</h2></b></center>';
 echo '<table width="80%" align="center" border="1">';
 echo '<tr>
           <td width=10% align=center><b>Physician</b></td>
           <td width=5% align=center><b>Year</b></td>
           <td width=5% align=center><b>Month</b></td>
           <td width=5% align=center><b>Day</b></td>
           <td width=75%><b>Comment</b></td>
           </tr>';
 
 $previousMeetingsStatement = "SELECT * 
                               FROM meetings
                               ORDER BY yearnumber DESC, 
                                        monthnumber DESC, 
                                        daynumber DESC
                               LIMIT 20";
 $previousMeetingsQuery = mysql_query($previousMeetingsStatement);
 while ($previousMeetingResult = mysql_fetch_array($previousMeetingsQuery))
 {
     echo '<tr>
           <td width=10% align=center>'.$previousMeetingResult['mdinitials'].'</td>
           <td width=5% align=center>'.$previousMeetingResult['yearnumber'].'</td>
           <td width=5% align=center>'.$previousMeetingResult['monthnumber'].'</td>
           <td width=5% align=center>'.$previousMeetingResult['daynumber'].'</td>
           <td width=75%>'.$previousMeetingResult['commnt'].'</td>
           </tr>';
 }
 echo '</table>';

  include ('includes/footer.html');
}

?>
<?php
session_start();
require ('checkForHoliday.php');

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
    VALUES ('{$_SESSION['initials']}','', 'unwanted2.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
    
    
    
   $page_title = 'Unscheduled Vacation Assignment';
   echo '<title>'.$page_title.'</title>';
   
   
   
   require_once ($_SESSION['login2string']);
   include ('includes/header.php');
   
   
   
   require_once 'menuBar.php';
   menuBar(255);
   
   $udate = $_REQUEST['udate'];
   $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));

   
   for ($y=0; $y<$udate; $y++)
   {
        $datetime->modify("+1 day");
   }
   
   $dyear = $datetime->format('Y');
   $dm = $datetime->format('n');
   $dd = $datetime->format('j');
   
   if ($datetime->format('N') > 5)
   {
       echo '<br><h2><center>This is a weekend.  
             Unscheduled vacation cannot be assigned on weekends.
             <br>Please try again.</center></h2><br>';
   }
   else if (checkForHoliday($dyear, $dm, $dd)) {
        echo '<br><h2><center>This is a holiday.  
             Unscheduled vacation cannot be assigned on holidays.
             <br>Please try again.</center></h2><br>';          
   }
   else
   { 
   $_SESSION['unwantyear'] = $dyear;
   $_SESSION['unwantmonth'] = $dm;
   $_SESSION['unwantday'] = $dd;
           
   /*
    * Select the list of anesthesiologists which are due for
    * unwanted vacation (mdnumber and indexx)
    */
   $uliststatement = "SELECT * FROM unwantedvac ORDER BY indexx LIMIT 200";
   $ulistquery = mysql_query($uliststatement);
   $numun = 0;
   $numun2 = 0;
   while ($ulistans = mysql_fetch_row($ulistquery))
   {
       $mdn = $ulistans[0];
       $mdindex = $ulistans[1];
       /*
        * see what assignment this anesthesiologist has
        * Need to order by beginblock to make sure the day part of the 
        * assignment is checked in cases of C OR Day, etc.
        */
       $checkstatement = "SELECT assignment, beginblock
                          FROM monthassignment 
                          WHERE monthnumber = {$dm}
                          AND daynumber = {$dd}
                          AND yearnumber = {$dyear}
                          AND mdnumber = {$mdn}
                          ORDER BY beginblock";
       $checkquery = mysql_query($checkstatement);
       $numrows = mysql_num_rows($checkquery);
       
       $checkans = mysql_fetch_row($checkquery);
       $assignu = trim($checkans[0]);
       $bb = $checkans[1];
       
       /*
        * Shows whether the assignment is eligible for unwanted status
        * 0 = no
        * 1 = yes
        * 2 = possible
        */
       $uw = 0;
       $checkustatement = "SELECT unwanted ".
                          "FROM assignments ".
                          "WHERE ".
                          "(".
                          " assignment LIKE '{$assignu}%' ".
                          " AND beginblock = ".$bb.
                          " AND weekend = 0 ".
                          ")";
       $checkuquery = mysql_query($checkustatement);
       if ($checkuquery) {
           $checkuans = mysql_fetch_array($checkuquery);
           $uw = $checkuans['unwanted'];
       }
       
       
       /*
        * if the unwanted status is 1
        */
       if ($uw == 1)
       {
           $repeat = false;
           /*
            * make sure an anesthesiologist isn't repeated in the list 
            */
           for ($z = 0; $z < $numun; $z++)
           {
               //$mdn is the current possible anesthesiologist who could get 
               //called off
                if ($mdn == $md[$z])
                {
                   $repeat=true;
                   break;
                }
           }
           /*
            * if this anesthesiologist is not a repeat, add to the list
            * of potential unwanted vacation anesthesiologists
            */
           if ($repeat==false)
           {
                $md[$numun] = $mdn;
                $mdindexx[$numun] = $mdindex;
                $mda[$numun] = $assignu;
                $numun++;
           }
       }
       else if ($uw == 2)
       {
           $repeat = false;
           /*
            * make sure an anesthesiologist isn't repeated in the list 
            */
           for ($z = 0; $z < $numun2; $z++)
           {
               //$mdn is the current possible anesthesiologist who could get 
               //called off
                if ($mdn == $md2[$z])
                {
                   $repeat=true;
                   break;
                }
           }
           /*
            * if this anesthesiologist is not a repeat, add to the list
            * of potential unwanted vacation anesthesiologists
            */
           if ($repeat==false)
           {
                $md2[$numun2] = $mdn;
                $mdindexx2[$numun2] = $mdindex;
                $mda2[$numun2] = $assignu;
                $numun2++;
           }
       }
   }
   
   
   
   echo '<br><br>
         <table width="100%">
         <tr>
         <td align="center">List of Anesthesiologists Eligible For Unscheduled Vacation</td>
         </tr>
         <tr>
         <td align="center">(with assignments allowing call off and in order of priority)</td>
         </tr>
         </table>';
   
   
   echo '<br><br>
         <table class="table3">
             <tr>
             <th><h4>Number</h4></td>
             <th><h4>Anesthesiologist</h4></td>
             <th><h4>Assignment</h4></td>  
             </tr>';
         
   for ($x = 0; $x < $numun; $x++)
   {
       $s = "SELECT last FROM mds WHERE number = {$md[$x]}";
       $q = mysql_query($s);
       $a = mysql_fetch_row($q);
       $mdlast = $a[0];
       
       
       echo '<tr>
             <td>'.$md[$x].'</td>
             <td>'.$mdlast.'</td>
             <td>'.$mda[$x].'</td>   
             </tr>';
   }
   echo '</table><br><br>';
   
   
    echo '<br><br>
         <table width="100%">
         <tr>
         <td align="center">List of Anesthesiologists Not Normally Eligible For Unscheduled Vacation</td>
         </tr>
         <tr>
         <td align="center">(with assignments not usually allowing Unwanted Vacation - Use Cautiously)</td>
         </tr>
         </table>';
    
    echo '<br><br>
         <table class="table3">
             <tr>
             <th><h4>Number</h4></td>
             <th><h4>Anesthesiologist</h4></td>
             <th><h4>Assignment</h4></td>    
             </tr>';
   
   for ($x = 0; $x < $numun2; $x++)
   {
       $s = "SELECT last FROM mds WHERE number = {$md2[$x]}";
       $q = mysql_query($s);
       $a = mysql_fetch_row($q);
       $mdlast = $a[0];
       
       
       echo '<tr>
             <td>'.$md2[$x].'</td>
             <td>'.$mdlast.'</td>
             <td>'.$mda2[$x].'</td>    
             </tr>';
   } 
   echo '</table><br><br>';
   

   echo "<br><br><h2><center>Select Anesthesiologist For Unscheduled Vacation: 
            </center></h2><br><br>";
   
   echo'<form method="post" action="unwanted3.php">';
   echo'
		<table align="center" class="content" border="0"
                    width="100%" bordercolor="#000000">
                    <tr>
			<td width="33%"></td>
			<td width="33%" align="center"> Choose Anesthesiologist: </td>
			<td width="33%"></td>
                    </tr>
                    <tr>
                    </tr>';
   
   echo '           <tr>';
   echo'
		      <td width="33%"></td>
		      <td width="33%" align="center">
                      <select name="umd">';
   
   for ($x = 0; $x < $numun; $x++)
   {      
       echo '<option value = '.$mdindexx[$x].'>'.$md[$x].'</option>';
   }
   
   for ($x = 0; $x < $numun2; $x++)
   {      
       echo '<option value = '.$mdindexx2[$x].'>'.$md2[$x].'</option>';
   }
   
   echo '<td width="33%"></td></tr></select>';

   echo '<tr height="40px">
         </tr>
         <tr>
               <td width="33%">
               </td>
               <td align="center" style="border-style:solid;border-color:#D7DAE1;
               background-color:#D7DAE1">
               <input type="submit" name="submit" value="Submit" 
                       class="btn">
               </td>
               <td width="33%">
               </td>
               </tr>';
   echo '</table></div></form><br><br>';
   }

   
   include ('includes/footer.html');
}
?>

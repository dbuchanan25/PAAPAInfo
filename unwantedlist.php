<?php
session_start();

echo'
    <link rel="stylesheet" href="style.css" type="text/css">
    ';


/*
 * Version 02_01
 */
/*
 * Last Revised 2012-09-07
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
    
    
   require_once ($_SESSION['login2string']);
   require_once ('menuBar.php');
   echo '<h1><center>Presbyterian Anesthesia Associates, PA</center></h1>';
   echo '<h2><center>Unscheduled Vacation List</center></h2>';
   echo '<h2><center>This list is for review only!  
         This is not the list to assign unwanted/unscheduled vacation!</h2>';
   echo '<h2><center>(Go to "Assign Unwanted Vacation" to assign unwanted/unscheduled vacation!)
         </h2><br><br><br>';
   
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'unwantedlist.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
   
   echo'
        <body><center>';
   
   /*
    * menuBar is called to set the proper menu tabs
    */
   menuBar(1583);
   
   echo'
        </div><br><br>';
   
   echo '<table class="table3">';
   echo '<tr><th width="100%" align="center">Available For Unwanted Vacation</th></tr>
         </table>';
   
   echo '<table class="table3">';
   echo '<tr border="1" bordercolor="black">

             <th>Anesthesiologist Number</th>
             <th>Anesthesiologist</th>
             <th>List Order</th>

             </tr>';
       
   $s1 = "SELECT * FROM unwantedvac ORDER BY indexx LIMIT 30";
   $q1 = mysql_query($s1);
   while ($a1 = mysql_fetch_row($q1))
   {
       $s3 = "SELECT last FROM mds where number = {$a1[0]}";
       $q3 = mysql_query($s3);
       $a3 = mysql_fetch_row($q3);
       echo '<tr border="1" bordercolor="black">

             <td>'.$a1[0].'</td>
             <td>'.$a3[0].'</td>    
             <td>'.$a1[1].'</td>

             </tr>';
   }
   echo '</table><br><br>';
   
   echo '<table class="table4">';
   echo '<tr><th>Recent Unwanted Vacation Assignments
         </th></tr></table>';
   
   echo '<table class="table4">';
   echo '<tr>
             <th>Anesthesiologist Number</th>
             <th>Anesthesiologist</th>
             <th>List Order</th>
             <th>Day</th>
             <th>Month</th>
             <th>Year</th>
             </tr>';
   
   $s2 = "SELECT * FROM unwantedvacpast ORDER BY year DESC, month DESC, day DESC, md";
   $q2 = mysql_query($s2);
   
   for ($x=0; $x<30; $x++)
   {
       $a2 = mysql_fetch_row($q2);
       $s4 = "SELECT last FROM mds where number = {$a2[0]}";
       $q4 = mysql_query($s4);
       $a4 = mysql_fetch_row($q4);
       echo '<tr>
                 <td>'.$a2[0].'</td>
                 <td>'.$a4[0].'</td>
                 <td>'.$a2[1].'</td>
                 <td>'.$a2[2].'</td>
                 <td>'.$a2[3].'</td>
                 <td>'.$a2[4].'
             </tr>';
   }
   echo '</table><br><br>';
   
   

   include ('includes/footer.html');
}
?>

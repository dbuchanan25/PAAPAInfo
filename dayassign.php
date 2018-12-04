<?php

/*
 * Version 02_02
 */
/*
 * LAST REVISED 2013-05-08
 */
/*
 * Revised 2011-08-23 so if it is a weekend, a day assignment is not displayed on the
 * day_display page.
 */
/*
 * Revised on 2011-08-27 to allow color change when hovering over "Change Assignment" button.
 */
function dayassign ($day1, $trhbt, $trhet, $day2, $trhbt2, $trhet2)
{
///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        DAY ASSIGNMENT DISPLAY CODE                                        //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
if ($_SESSION['weekend']==1)
    return;
$beginTimeCallStatement = "SELECT time
                           FROM timeperiods
                           WHERE timeperiod=$trhbt";
$beginTimeCallQuery = mysql_query($beginTimeCallStatement);
$beginTimeCallResult = @mysql_fetch_array($beginTimeCallQuery);

$endTimeCallStatement = "SELECT time
                         FROM timeperiods
                         WHERE timeperiod=$trhet";
$endTimeCallQuery = mysql_query($endTimeCallStatement);
$endTimeCallResult = @mysql_fetch_array($endTimeCallQuery);

$beginTimeCallStatement2 = "SELECT time
                            FROM timeperiods
                            WHERE timeperiod=$trhbt2";
$beginTimeCallQuery2 = mysql_query($beginTimeCallStatement2);
$beginTimeCallResult2 = @mysql_fetch_array($beginTimeCallQuery2);

$endTimeCallStatement2 = "SELECT time
                          FROM timeperiods
                          WHERE timeperiod=$trhet2";
$endTimeCallQuery2 = mysql_query($endTimeCallStatement2);
$endTimeCallResult2 = @mysql_fetch_array($endTimeCallQuery2);

echo '
<form method="link" action="day.php">
<table align="center" class="content" width="100%">   
    <td align="center" width="21%" 
        onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
        <input type="submit" name="homechange" value="Change Assignment" />
    </td>

    <td align="center" height="25" width="16%" style="color:black; background-color:#D7DAE1">
    Day:
    </td>
	  
    <td align="center" height="25" width="16%" style="color:black">';


    if (empty($day1))
         echo '<b>None</b>';
    else
         echo '<b>'.$day1.'</b>';
   
echo'
    </td>

    <td align="center" height="25" width="10%" style="color:black; background-color:#D7DAE1">
    Begin Time:
    </td>

    <td align="center" height="25" width="10%" style="color:black">';
   
    if (!empty($beginTimeCallResult['time']))
      echo $beginTimeCallResult['time'];
   
echo'
    </td>

    <td align="center" height="25" width="10%" style="color:black; background-color:#D7DAE1">
    End Time:
    </td>

    <td align="center" height="25" width="10%" style="color:black">';
   
    if (!empty($endTimeCallResult['time']))
      echo $endTimeCallResult['time'];
	  
echo'
    </td>';


   if (!empty($day2))
   {
echo'
       <tr>
       <td></td>
       <td align="center" height="25" width="16%" style="color:black; background-color:#D7DAE1">
       Day 2:
       </td>

       <td align="center" height="25" width="16%" style="color:black">';

       echo '<b>'.$day2.'</b>';

echo'
       </td>

       <td align="center" height="25" width="10%" style="color:black; background-color:#D7DAE1">
       Begin Time:
       </td>

       <td align="center" height="25" width="10%" style="color:black">';

       if (!empty($beginTimeCallResult2['time']))
          echo $beginTimeCallResult2['time'];

echo'
       </td>

       <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
       End Time:
       </td>

       <td align="center" height="25" width="10%" style="color:black">';

       if (!empty($endTimeCallResult2['time']))
          echo $endTimeCallResult2['time'];

echo'
       </td>
       </tr>';
   }

echo'
    </table>
    </form>';


///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        END OF DAY ASSIGNMENT DISPLAY CODE                                 //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
}
?>

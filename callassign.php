<?php

/*
 * Version 02_01
 */
/*
 * LAST REVISED 2011-08-27
 */
/*
 * REVISION ON 2011-06-13 WAS TO CHANGE THE LABEL FROM HOMECALL TO CALL TO HELP ACCOMODATE THE
 * NEW PAY RULES
 */
/*
 * Revision on 2011-07-18
 *      -Renamed function to callassign() from homeassign()
 *      -Allowed for separate $call2 from $call1 with proper display
 */
/*
 * Revised on 2011-08-27 to allow color change when hovering over the "Change Assignment" button.
 */
function callassign ($call1, $trhbt, $trhet, $call2, $trhbt2, $trhet2)
{
///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        HOME CALL DISPLAY CODE                                             //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
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
<form method="link" action="home.php">
<table align="center" class="content" border="0" width="100%" bordercolor="#000000">   
    <td align="center" width="21%" 
        onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
        <input type="submit"
        name="homechange" value="Change Assignment" />
    </td>

    <td align="center" height="25" width="16%" style="color:black; background-color:#D7DAE1">
    Call:
    </td>
	  
    <td align="center" height="25" width="16%" style="color:black">';
   
    if (empty($call1))
         echo '<b>None</b>';
    else
         echo '<b>'.$call1.'</b>';
   
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


   if (!empty($call2))
   {
echo'
       <tr>
       <td></td>
       <td align="center" height="25" width="16%" style="color:black; background-color:#D7DAE1">
       Call 2:
       </td>

       <td align="center" height="25" width="16%" style="color:black">';

       echo '<b>'.$call2.'</b>';

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

       <td align="center" height="25" width="10%" style="color:black; background-color:#D7DAE1">
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
//                        END OF HOME CALL DISPLAY CODE                                      //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
}
?>

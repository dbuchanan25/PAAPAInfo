<?php
function primaryassign ($y, $trpbt, $trpet)
{
///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        PRIMARY ASSIGNMENT DISPLAY CODE                                    //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////

$beginTimeDayStatement = "SELECT time
                          FROM timeperiods
                          WHERE timeperiod=$trpbt";
$beginTimeDayQuery = mysql_query($beginTimeDayStatement);
$beginTimeDayResult = @mysql_fetch_array($beginTimeDayQuery);

$endTimeDayStatement = "SELECT time
                        FROM timeperiods
                        WHERE timeperiod=$trpet";
$endTimeDayQuery = mysql_query($endTimeDayStatement);
$endTimeDayResult = @mysql_fetch_array($endTimeDayQuery);


echo'
<form method="link" action="primary.php">
<table align="center" width="100%" border="0" bordercolor="#000000">
   
   <td align="center" width="21%">
   <input type="submit" value="Change Assignment" />
   </td>
';
echo '
   <td align="center" height="25" width="16%" style="color:black; background-color:#E5E5E5">
   Day Assignment:
   </td>  
	  
   <td align="center" height="25" width="16%" style="color:black">';
   /*
   if (empty($y))
   	 echo '<b>None</b>';
   else
    * 
    */
   	 echo '<b>'.$y.'</b>';
   
   echo'
   </td>
    
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   Begin Time:
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black">';
   
   if (!empty($beginTimeDayResult['time']))
      echo $beginTimeDayResult['time'];
	  
   echo'
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   End Time:
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black">';
   
   if (!empty($endTimeDayResult['time']))
      echo $endTimeDayResult['time'];
	  
   echo'
   </td>
   

</table></form>'; 


///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        END OF PRIMARY ASSIGNMENT DISPLAY CODE                           //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
}
?>

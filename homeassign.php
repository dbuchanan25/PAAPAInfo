<?php
function homeassign ($hy, $trhbt, $trhet, $trhbt2, $trhet2)
{
///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        HOME CALL DISPLAY CODE                                             //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////

echo '<form method="link" action="home.php">

<table align="center" class="content" border="0" width="100%" bordercolor="#000000">
   
   <td align="center" width="21%"><input type="submit" name="homechange" value="Change Assignment" />
   </td>
   
   
   <td align="center" height="25" width="16%" style="color:black; background-color:#E5E5E5">
   Home Call:
   </td>  
	  
   <td align="center" height="25" width="16%" style="color:black">';
   
   if (empty($hy))
   	 echo '<b>None</b>';
   else
   	 echo '<b>'.$hy.'</b>';
   
   echo'
   </td>
    
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   Begin Time:
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black">';
   
   if (!empty($trhbt))
      echo $trhbt;
   
   echo'
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   End Time:
   </td>
        	  
        	  
   <td align="center" height="25" width="10%" style="color:black">';
   
   if (!empty($trhet))
      echo $trhet;
	  
   echo'
   </td>';
   
   if (!empty($trhbt2))
   {
   echo'
   <tr>
   <td></td>
   <td></td>
   <td></td>
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   Begin Time:
   </td>        	  
   <td align="center" height="25" width="10%" style="color:black">';
      echo $trhbt2;  
   echo'
   </td>        	  
   <td align="center" height="25" width="10%" style="color:black; background-color:#E5E5E5">
   End Time:
   </td>     	  
   <td align="center" height="25" width="10%" style="color:black">';   
   if (!empty($trhet2))
      echo $trhet2;
   }
	  
   echo'
   </td>
   </tr>
   
</table></form>';
	  


///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        END OF HOME CALL DISPLAY CODE                                      //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
}
?>

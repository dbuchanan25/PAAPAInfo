<?php

function menubar($bitS)
{
    
echo'
<link rel="stylesheet" href="styleORM.css">
';
echo '<table align="center" width="100%">
        <form method="post" action="menuResults.php" class="input">
        <tr>';
        //00000000001 - 1
        if ((($bitS >> 0) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" value="Main Menu" class="btn">
            </td>';
        }
        //00000000010 - 2
        if ((($bitS >> 1) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Schedule For Page" class="btn">
            </td>';
        }
        //00000000100 - 4
        if ((($bitS >> 2) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Complete Month" class="btn">
            </td>';
        }
        //00000001000 - 8
        if ((($bitS >> 3) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="ORMGR Worksheet" class="btn">
            </td>';
        }
        //00000010000 - 16
        if ((($bitS >> 4) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Meeting Notification" class="btn">
            </td>';
        }
        //00000100000 - 32
        if ((($bitS >> 5) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Assign Unscheduled Vacation" class="btn">
            </td>';
        }
        //00001000000 - 64
        if ((($bitS >> 6) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Unscheduled Vacation List" class="btn">
            </td>';
        }
        //00010000000 - 128
        if ((($bitS >> 7) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Advance Two Days" class="btn">
            </td>';
        }
        //00100000000 - 256
        if ((($bitS >> 8) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Print Friendly Version" class="btn">
            </td>';
        }
         //100000000000 - 2048
        if ((($bitS >> 11) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="ORMAssignment" class="btn">
            </td>';
        }
        //01000000000 - 512
        if ((($bitS >> 9) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Help" class="btn">
            </td>';
        }
        //10000000000 - 1024
        if ((($bitS >> 10) & 1) == 1){
            echo'
            <td align="center">
            <input type="submit" name="Me2" 
                    value="Logout" class="btn">
            </td>';
        }
        
        
echo'            
        </tr>
        </form>

        <tr height=25px>
            <td style="border:none;"></td>
        </tr>
</table>';
  
}
 


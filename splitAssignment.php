<?php
/*
 * Version 02_01
 * Last Revised 2011-08-23
 */
/*
 * Revised on 2011-08-23 so the possibility of a Day Assignment Split cannot be made on a
 * week-end.
 */
function splitAssignment()
{
    echo'
                <br><br>
                <table height=30px align=CENTER width=100%
                    style="font-size:14; background-color=#ffffff">
                    <tr>
                        <td width=18%></td>
                        <td align="center" 
                                style="width:30%; height:30px; background-color:D7DAE1;
                                border-style:solid;border-color:#000000;">
                            Split Assignment with Another Partner:
                        </td>
                        <td width=4%>
                        <form method="link" action="split.php">';


    if ($_SESSION['weekend']==0)
    {
        echo'    
                            <td align="center" 
                                style="width:15%; height:30px;"
                                onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                                    <input type="submit" name="assignmentSplit"
                                        value="Day Assignment"/>
                            </td>
             ';
     }

        echo'
                            <td align="center" 
                                style="width:15%; height:30px;"
                                onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                                    <input type="submit" name="assignmentSplit"
                                        value="Call Assignment"/>
                            </td>
                            <td width=18%></td>
                        </form>
                    </tr>
                 </table>';
}

?>

<?php
function addhours ()
{

/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-27
 */
/*
 * Revised 2011-08-27 to allow color change when hovering over "Change or Add Hours"
 */
/*
///////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                           //
//                        ADDED HOURS DISPLAY CODE                                           //
//                                                                                           //
///////////////////////////////////////////////////////////////////////////////////////////////
 */

/* 
   Begin the added hours with up to 3 separate times for added hours
*/

/*
///////////////////////////////////////////////////////////////////////////////////////////////
//First loop                                                                                 //
///////////////////////////////////////////////////////////////////////////////////////////////
 */

	  
    $ah = mysql_query ("SELECT assignment
                        FROM monthassignment
                        WHERE yearnumber={$_SESSION['dty']}
                        AND monthnumber={$_SESSION['dtm']}
                        AND daynumber={$_SESSION['dai']}
                        AND assigntype=4
                        AND mdnumber=(SELECT number
                                      FROM mds
                                      WHERE initials='{$_SESSION['schedmd']}')
                        ORDER BY bt");
    $ahf = @mysql_fetch_row($ah);
    $_SESSION['ahreason1']=$ahf[0];

    $aht = mysql_query ("SELECT beginblock, bt, endblock, et
                         FROM monthassignment
                         WHERE yearnumber={$_SESSION['dty']}
                         AND monthnumber={$_SESSION['dtm']}
                         AND daynumber={$_SESSION['dai']}
                         AND assigntype=4
                         AND mdnumber=(SELECT number
                                       FROM mds
                                       WHERE initials='{$_SESSION['schedmd']}')
                         ORDER BY bt");

    if (empty($ahf[0]))
    {
        echo'
                <table align="center" border="0" width="100%" bordercolor="#000000">
                    <tr>
                    <form method="link" action="add.php">
                        <td align="center" width="21%"
                        onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                        <input type="submit" value="Change or Add Hours" />
                        </td>
           
                        <td align="center" height="25" width="16%" style="color:black;
                        background-color:#D7DAE1">
                            Added Hours:
                        </td>
                        <td align="center" height="25" width="16%" style="color:black">
                            <b>
                                None
                            </b>
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            Begin Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            End Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                        </td>
                    </tr>
                </table>
                </form>
      ';
    }
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If they have been added.........Round 1.....Place the results and make it possible to add more./
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    else
    {
        $ahft1 = mysql_fetch_array($aht);
        $_SESSION['ah1bt']=$ahft1['beginblock'];
        $_SESSION['ah1et']=$ahft1['endblock'];
   
        echo'
                <table align="center" border="0" width="100%" bordercolor="#000000">
                    <tr>
                    <form method="link" action="add.php">
                        <td align="center" width="21%"
                            onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                        <input type="submit" value="Change or Add Hours" />
                        </td>
           
                        <td align="center" height="25" width="16%" style="color:black;
                        background-color:#D7DAE1">
                            Added Hours:
                        </td>
                        <td align="center" height="25" width="16%" style="color:black">
                            <b>'.
                                $ahf[0].'
                            </b>
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            Begin Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft1['bt'].'
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            End Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft1['et'].'
                        </td>
                    </tr>
                </table>
                </form>
            ';

/*
//Test again for more results, if there, display them and make it possible to add more....Part 2   
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
   
  
        $ahf = mysql_fetch_array($ah);
        $_SESSION['ahreason2']=$ahf[0];
        if (!empty($ahf[0]))
        {
            $ahft2 = mysql_fetch_array($aht);
            $_SESSION['ah2bt']=$ahft2['beginblock'];
            $_SESSION['ah2et']=$ahft2['endblock'];
          
		  
            echo'
                <table align="center" border="0" width="100%" bordercolor="#000000">
                    <tr>
                        <td align="center" width="21%"
                            onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                        </td>
                        <td align="center" height="25" width="16%" style="color:black;
                        background-color:#D7DAE1">
                            Added Hours:
                        </td>
                        <td align="center" height="25" width="16%" style="color:black">
                            <b>'.
                                $ahf[0].'
                            </b>
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            Begin Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft2['bt'].'
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            End Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft2['et'].'
                        </td>
                    </tr>
                </table>
            ';




            $ahf = mysql_fetch_array($ah);
            $_SESSION['ahreason3']=$ahf[0];
            if (!empty($ahf[0]))
            {
                $ahft3 = mysql_fetch_array($aht);
                $_SESSION['ah3bt']=$ahft3['beginblock'];
                $_SESSION['ah3et']=$ahft3['endblock'];

                                    //<form method="link" action="add.php">
                                    //</form>
                                    //<input type="submit" value="Change or Add Hours" />

                echo'
                    <table align="center" border="0" width="100%" bordercolor="#000000">
                        <td align="center" width="21%"
                            onMouseOver="bgColor=\'blue\'" onMouseOut="bgColor=\'#ffffff\'">
                        </td>
                        <td align="center" height="25" width="16%" style="color:black;
                        background-color:#D7DAE1">
                            Added Hours:
                        </td>
                        <td align="center" height="25" width="16%" style="color:black">
                            <b>'.
                                $ahf[0].'
                            </b>
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            Begin Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft3['bt'].'
                        </td>
                        <td align="center" height="25" width="10%" style="color:black;
                        background-color:#D7DAE1">
                            End Time:
                        </td>
                        <td align="center" height="25" width="10%" style="color:black">
                            '.
                            $ahft3['et'].'
                        </td>
                    </tr>
                </table>
                ';
            }
        }
    }
}
?>
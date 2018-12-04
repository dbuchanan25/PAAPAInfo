<?php 
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-06-27
 */
/*
 * Called from selecttimeshome.js
 */

/*
 * REVISED 2011-06-27 TO SEND BACK THE timeperiod INSTEAD OF THE time
   //REVISED 2011-06-11 TO UPDATE CONNECTION FOR LOCAL VS. WEB SERVERS AND TO GENERALLY UPDATE
   //FOR THE NEW PAY SYSTEM.
 *
 */

    $q=$_GET["q"];

   
   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
    $con = @mysql_connect('localhost', 'paapaus_dcb', 'srt101');

    if ($con)
       $db_selected = @mysql_select_db("paapaus_anesthesiapay", $con);
    if (!isset($db_selected))
    {
        //LOCAL CONNECTION
        $conlocal = @mysql_connect('localhost', 'root', '');
        if ($conlocal)
            $db_selected_local = @mysql_select_db("anesthesiapay", $conlocal);
        else if (!isset($db_selected) && !isset($db_selected_local))
        {
            die('Could not connect: ' . mysql_error());
        }
    }

    $sqlstatement=("SELECT beginblock, endblock
                    FROM assignments
                    WHERE n=$q");
    $sqlq = mysql_query($sqlstatement);
    $sqlf = @mysql_fetch_row($sqlq);


    $callassignment2_usualbeginningtimeperiods=mysql_query
                    ("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>=$sqlf[0]
                      AND timeperiod<$sqlf[1]");

    $callassignment2_usualendingtimeperiods=mysql_query
                    ("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>$sqlf[0]
                      AND timeperiod<=$sqlf[1]");

    $e = mysql_query("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod=$sqlf[1]");

    if (!empty($e))
        $end = @mysql_fetch_row($e);



    echo'
            <td align="left" width="683">
                <table border="0">
                    <td align="right" height="25px" width="100px" style="color:black">
                        Begin Time:
                    </td>
                    <td align="center" height="25px" width="100px" style="color:black">
                    <select name="btcall2">
        ';

    while($row2 = @mysql_fetch_row($callassignment2_usualbeginningtimeperiods))
    {
        echo "<option value=$row2[1]>$row2[0]</option>\n";
    }

    echo '          </select>
                    </td>
                    <td align="right" height="25" width="100" style="color:black">
                        End Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="etcall2">
         ';

    while($row3 = @mysql_fetch_row($callassignment2_usualendingtimeperiods))
    {
        if (trim($row3[1])==trim($end[1]))
            echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
        else
            echo "<option value=$row3[1]>$row3[0]</option>\n";
    }

    echo '          </select>
                    </td>
                </table>
            </td>';

    if ($con)
        mysql_close($con);
    else
        mysql_close($conlocal);
?>
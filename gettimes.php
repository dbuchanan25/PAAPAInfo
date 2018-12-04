<?php 

/*
 * VERSION 02_01
 */
/*
 * Last Revised 2011-06-12
 */
/*
 * REVISED 2011-06-12 TO UPDATE THE CONNECTIONS TO THE WEB AND LOCAL SERVERS AND GENERALLY
 * UPDATE FOR THE NEW PAY SYSTEM.
 */
/*
 * Called from selettimes.js
 */


   $q=$_GET["q"];


   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   $con = mysql_connect('localhost', 'paapaus_dcb', 'srt101');
   //LOCAL CONNECTION
   //$con = mysql_connect('localhost', 'root', 'srt101');
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }

   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   mysql_select_db("paapaus_anesthesiapay", $con);
   //LOCAL CONNECTION
   //mysql_select_db("anesthesiapay", $con);
   
    $sqlstatement=("SELECT beginblock, endblock
                    FROM assignments
                    WHERE n=$q");

    $sql=mysql_query($sqlstatement);
    $sqlf = mysql_fetch_row($sql);
   
   
    $tr1=mysql_query("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>=$sqlf[0]
                      AND timeperiod<$sqlf[1]");
					 
    $tr2=mysql_query("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>$sqlf[0]
                      AND timeperiod<=$sqlf[1]");
 
    $e = mysql_query("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod=$sqlf[1]");

    $end = mysql_fetch_row($e);

   
    echo'
            <td align="left" width="683">
                <table border="0">
                    <td align="right" height="25px" width="100px" style="color:black">
                        Begin Time:
                    </td>
                    <td align="center" height="25px" width="100px" style="color:black">
                    <select name="btprimary">
	';
	  
    while($row2 = mysql_fetch_row($tr1))
    {
        echo "<option value=$row2[1]>$row2[0]</option>\n";
    }

    echo '          </select>
                    </td>
                    <td align="right" height="25" width="100" style="color:black">
                        End Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="etprimary">
	 ';
	    
    while($row3 = mysql_fetch_row($tr2))
    {
        if (trim($row3[1])==trim($end[1]))
            echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
        else
            echo "<option value=$row3[1]>$row3[0]</option>\n";
    }

    echo '          </select>
                    </td>
                </table>
            </td>
         ';

    mysql_close($con);
?>
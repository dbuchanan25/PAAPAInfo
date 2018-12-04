<?php

session_start();

/*
 * VERSION 02_01
 */
/*
 * LAST REVISED 2013-05-09
 */
/*
 * REVISED 2011-06-12 TO UPGRADE FOR THE NEW PAY RULES
 */
/*
 * REVISED 2011-06-26 TO BETTER FORMAT AND CHECK CONSISTENCY OF HTML FORMATTING
 */
/*
 * Revised 2011-07-18 Corrected display of the second call option and corrected some of the
 * variable names, especially in Section 9.
 */
/*
 * Revised 2011-07-23/24  Got AJAX correct about being able to instantly update the acceptable
 * times for call assignments
 */
/*
 * Revised 2011-08-13 to have a 'None' display correctly, especially the endtime.
 */
/*
 * Revised 2011-08-21 to correct an error near line 300 in Section 8
 * if (trim($result1_array[0]) == trim($_SESSION['callassignment1']))
 * Didn't have the "trim" conditions which led to a mismatch.
 */
/*
 * Revised 2013-04-29 to center the table below the bar of color 15 minute segments.
 */

/*
 * Section 1 Check to see if the user is logged in.
 */
if (!isset($_SESSION['initials']))
{
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}

/*
 * Section 2
 */
else
{
 /*
  * All INCLUDES
  */
    include ('includes/header.php');
    require_once ($_SESSION['login2string']);

    include ('primaryassign.php');

    include ('day_display_code1.php');
    include ('day_display_code2.php');

    include ('isweekend.php');
    include ('getBeginTimeTimePeriods.php');
    include ('getEndTimeTimePeriods.php');


    /*
     * Section 3
     */

    $datet = new DateTime();
    $datet->setDate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);


    /*
     * Section 4
     */


/*
    if (isset($_SESSION['callassignment1']))
    {
 *
 */
/*
 * Usual begin and end blocks are obtained from table "assignments" for
 * $_SESSION['callassignment1']
 * Then get the time period span for this assignment.
*/
    /*


        $usualbeginendblocksforcallquery = "SELECT beginblock, endblock
                                            FROM assignments
                                            WHERE weekend={$_SESSION['weekend']}
                                            AND callassignment=1
                                            AND assignment='{$_SESSION['callassignment1']}'";
        $usualbeginendblocksforcallresult = mysql_query($usualbeginendblocksforcallquery);
        $usualbeginendblocksforcallarray = @mysql_fetch_row($usualbeginendblocksforcallresult);
        
        
        $callassignment1_usualbeginningtimeperiods = mysql_query
                    ("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>=$usualbeginendblocksforcallarray[0]
                      AND timeperiod<$usualbeginendblocksforcallarray[1]");

        $callassignment1_usualendingtimeperiods = mysql_query
                    ("SELECT time, timeperiod
                      FROM timeperiods
                      WHERE timeperiod>$usualbeginendblocksforcallarray[0]
                      AND timeperiod<=$usualbeginendblocksforcallarray[1]");
    }
     */
    /*
     * Section 7
     */
    echo '<form method="post" action="day_display.php">';

/*
 * Get a list of all possible call assignments
 */
    //var_dump($_SESSION['callassignment1']);
    //var_dump($_SESSION['callassignment2']);
    
/*
 * If $_SESSION['callassignment1'] is not set (empty) and
 * if $_SESSION['callassignment2'] is not set, then this happens.
 */
    if (!isset($_SESSION['callassignment1']) && !isset($_SESSION['callassignment2']))
    {
        $callassignmentslistquery   = mysql_query("SELECT DISTINCT assignment, n
                                                   FROM assignments
                                                   WHERE (
                                                            type_number=2
                                                            OR
                                                            type_number=6
                                                            OR
                                                            type_number=4
                                                         )
                                                   AND weekend={$_SESSION['weekend']}
                                                   ORDER BY assignment");

    /*
     * Section 6
     * Call to function "daydisplay()
     * daydisplay() was changed to day_display_code1() on 2011-07-20
     */
        day_display_code1($datet);





/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If a home assignment has not been previously chosen then this block of code allows the        //
//dynamic repopulation of the begin and end times using the javascript option.                  //
//////////////////////////////////////////////////////////////////////////////////////////////////
*
*/

        echo '  <head>
                <script src="selecttimeshome.js">
                </script>
                </head>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="right" width="582">
                            <table align="right" valign="top" width="582" border="0">
                                <td width="182">
                                </td>
                                <td align="right" height="25" width="200px">
                                    Call Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="callassignment1"
                                onchange="showTimesHome(callassignment1.value,1)">
             ';


            while ($row = @mysql_fetch_row($callassignmentslistquery))
            {
                if (trim($row[0]) == 'None')
                {
                    echo "<option selected='selected' value=$row[1]>$row[0]</option>\n";
                    $nNone = $row[1];
                } 
                else
                {
                    echo "<option value=$row[1]>$row[0]</option>\n";
                }
            }

        echo '
                                </select>
                                </td>
                            </table>
                        </td>
                        <td align="left" width="668">
                            <table align="left" border="0">
                                <tr>
                                    <td id="txtHint3" align="center" height="25px" width="400px">
                                        <table border="0">
                                            <td align="right" height="25px" width="100px"
                                            style="color:black">
                                                Begin Time:
                                            </td>
                                            <td align="center" height="25px" width="100px"
                                            style="color:black">
                                            <select name="btcall1">
            ';

        $beginTimeTPeriods = getBeginTimeTimePeriods($nNone);
        while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
        {
            echo "<option value=$row[1]>$row2[0]</option>\n";
        }

        echo '
                                    </select>
                                    </td>
                                    <td align="right" height="25" width="100" style="color:black">
                                        End Time:
                                    </td>
                                    <td align="center" height="25" width="100"
                                    style="color:black">
                                    <select name="etcall1">
             ';

        $endTimeTPeriods = getEndTimeTimePeriods($nNone);
        while ($row3 = @mysql_fetch_row($endTimeTPeriods))
        {
            if ($row3[1]==96)
                echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
            else
                echo "<option value=$row3[1]>$row3[0]</option>\n";
        }

        echo '
                                            </select>
                                            </td>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                    </td>
                </tr>
            </table>
              ';
    }

/*
* Section 8
//////////////////////////////////////////////////////////////////////////////////////////////////
//This part of the code is called if a first call option has already been chosen.               //
//This allows splitting the call into two segments                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
*
*/
    else
    {
        $result1=mysql_query("SELECT DISTINCT assignment, n
                               FROM assignments
                               WHERE (
                                        type_number=2
                                        OR
                                        type_number=6
                                        OR
                                        (type_number=4 AND assignment LIKE 'None')
                                     )
                               AND weekend={$_SESSION['weekend']}
                               ORDER BY assignment");

        day_display_code1($datet);
        

        echo '
                <head>
                <script src="selecttimeshome.js">
                </script>
                </head>
                <table align="center" border="0" width=100%>
                    <tr>
                        <td align="right" width="582">
                            <table align="right" valign="top" width="582" border="0">
                                <td width="182">
				</td>
                                <td align="right" height="25" width="200px">
                                    Call Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="callassignment1"
                                onchange="showTimesHome(this.value,1)">
             ';


     
    while ($result1_array = @mysql_fetch_row($result1))
    {
        if (trim($result1_array[0]) == trim($_SESSION['callassignment1']))
        {
          echo "<option value=$result1_array[1] selected='selected'>$result1_array[0]</option>\n";
          $n = $result1_array[1];
        }
        else
        {
            echo "<option value=$result1_array[1]>$result1_array[0]</option>\n";
        }
    }


 
     echo '
                                </select>
                                </td>
                            </table>
                        </td>
                        <td align="left" width="668">
                            <table align="left" border="0">
                                <tr>
                                    <td id="txtHint3" align="center" height="25px" width="400px">
                                        <table border="0">
                                            <td align="right" height="25px" width="100px"
                                            style="color:black">
                                                Begin Time:
                                            </td>
                                            <td align="center" height="25px" width="100px"
                                            style="color:black">
                                            <select name="btcall1">
             ';
	  
    $beginTimeTPeriods = getBeginTimeTimePeriods($n);
    while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
    {
        if (trim($row2[1])==trim($_SESSION['callassignment1beginblock']))
            echo "<option selected='selected' value=$row2[1]>$row2[0]</option>\n";
        else
            echo "<option value=$row2[1]>$row2[0]</option>\n";
    }

        echo '
                                            </select>
                                            </td>
                                            <td align="right" height="25" width="100"
                                            style="color:black">
                                                End Time:
                                            </td>
                                            <td align="center" height="25" width="100"
                                            style="color:black">
                                            <select name="etcall1">
             ';
	    
    $endTimeTPeriods = getEndTimeTimePeriods($n);
    while ($row3 = @mysql_fetch_row($endTimeTPeriods))
    {
        if (trim($row3[1])==trim($_SESSION['callassignment1endblock']))
            echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
        else
            echo "<option value=$row3[1]>$row3[0]</option>\n";
    }

        echo '
                                            </select>
                                            </td>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" width="582">
                            <table align="right" valign="top" width="582" border="0">
                                <td width="182">
				</td>
                                <td align="right" height="25" width="200px">
                                    Call Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="callassignment2"
                                onchange="showTimesHome(this.value,2)"';






/*
 * Section 9
 * Populate the second call option, if $_SESSION['callassignment2'] is set already, then display
 * it in the option box; otherwise display 'None'
 * If the $_SESSION['trhbt2'] variable is set, it is displayed in the beginning time box for the
 * second call option.
 * If the $_SESSION['trhet2'] variable is set, it is displayed in the ending time box for the
 * second call option.
 */


        $result2Statement = ("  SELECT DISTINCT assignment, n
                                FROM assignments
                                WHERE (
                                         type_number=2
                                         OR
                                         type_number=6
                                         OR
                                         (type_number=4 AND assignment LIKE 'None')
                                      )
                                AND weekend={$_SESSION['weekend']}
                                ORDER BY assignment");
        $result2=mysql_query($result2Statement);


        if (!isset ($_SESSION['callassignment2']))
        {
            
            $result2_array = @mysql_fetch_row($result2);
            
            echo "<option value=$result2_array[1]>$result2_array[0]</option>";
            do
            {                    
                    if (trim($result2_array[0]) === 'None')
                    {
                        echo "<option value=$result2_array[1] selected='selected'>
                               $result2_array[0]</option>";
                        $n = $result2_array[1];
                    }
                    else if (trim($_SESSION['callassignment1'])==='Peds Call' &&
                             (trim($result2_array[0]) === 'Peds Call Short'))
                    {
                        continue;
                    }
                    else
                    {
                        echo "<option value=$result2_array[1]>$result2_array[0]</option>";
                    }
            }
            while ($result2_array = @mysql_fetch_row($result2));
             
             echo '
                                </select>
                            </td>
                        </table>
                    </td>
                    <td align="left" width="668">
                        <table align="left" border="0">
                                <td id="txtHint4" align="center" height="25px" width="400px">
                                    <table border="0">
                                        <td align="right" height="25px" width="100px"
                                        style="color:black">
                                            Begin Time:
                                        </td>
                                        <td align="center" height="25px" width="100px"
                                        style="color:black">
                                        <select name="btcall2">
             ';
            $beginTimeTPeriods = getBeginTimeTimePeriods($n);
            
            
            while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
            {
                if ($row2[1]==0)
                    echo "<option selected='selected' value=$row2[1]>$row2[0]</option>\n";
                else
                    echo "<option value=$row2[1]>$row2[0]</option>\n";
            }

            echo '
                                            </select>
                                            </td>
                                            <td align="right" height="25" width="100"
                                            style="color:black">
                                                End Time:
                                            </td>
                                            <td align="center" height="25" width="100"
                                            style="color:black">
                                            <select name="etcall2">
                 ';
            $endTimeTPeriods = getEndTimeTimePeriods($n);
            while ($row3 = @mysql_fetch_row($endTimeTPeriods))
            {
                if ($row3[1]==96)
                    echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
                else
                    echo "<option value=$row3[1]>$row3[0]</option>\n";
            }


                echo '
                                                </select>
                                                </td>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                     ';

            }









        else
        {
            $result2_array = @mysql_fetch_row($result2);
            
            echo "<option value=$result2_array[1]>$result2_array[0]</option>";
            do
            { 
                if (trim($result2_array[0]) == $_SESSION['callassignment2'])
                {
                  echo "<option value=$result2_array[1] selected='selected'>
                    $result2_array[0]</option>\n";
                  $n = $result2_array[1];
                }
                else
                {
                    echo "<option value=$result2_array[1]>$result2_array[0]</option>\n";
                }
            }
            while ($result2_array = @mysql_fetch_row($result2));

        echo '
                                </select>
                            </td>
                        </table>
                    </td>
                    <td align="left" width="668">
                        <table align="left" border="0">
                                <td id="txtHint4" align="center" height="25px" width="400px">
                                    <table border="0">
                                        <td align="right" height="25px" width="100px"
                                        style="color:black">
                                            Begin Time:
                                        </td>
                                        <td align="center" height="25px" width="100px"
                                        style="color:black">
                                        <select name="btcall2">
             ';
	  
        $beginTimeTPeriods = getBeginTimeTimePeriods($n);
        while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
        {
            if (trim($row2[1])==trim($_SESSION['callassignment2beginblock']))
                echo "<option selected='selected' value=$row2[1]>$row2[0]</option>\n";
            else
                echo "<option value=$row2[1]>$row2[0]</option>\n";
        }

            echo '
                                            </select>
                                            </td>
                                            <td align="right" height="25" width="100"
                                            style="color:black">
                                                End Time:
                                            </td>
                                            <td align="center" height="25" width="100"
                                            style="color:black">
                                            <select name="etcall2">
                 ';

        $endTimeTPeriods = getEndTimeTimePeriods($n);
        while ($row3 = @mysql_fetch_row($endTimeTPeriods))
        {
            if (trim($row3[1])==trim($_SESSION['callassignment2endblock']))
                echo "<option selected='selected' value=$row3[1]>$row3[0]</option>\n";
            else
                echo "<option value=$row3[1]>$row3[0]</option>\n";
        }


            echo '
                                            </select>
                                            </td>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                 ';

        }
    }



	  
        echo  '
            <table align="center" width="100%">
                <tr style="height:30px">
                </tr>
                <tr>
                </tr>
                <tr>
                    <td align="center">
                    <input type="submit" style="width:175px; height:25px; font-size:medium"
			       name="submit" value="Submit Changes" />
                    </td>
                </tr>
	    </form>
            </table>
            <table>
                <tr>
                    <td height="25">
                    </td>
                </tr>
            </table>
             ';

    $_SESSION['changeassignment']=3;
    include ('includes/footer.html');
}
?>

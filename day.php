<?php

if (!isset($_SESSION)) { session_start(); }

/*
 * VERSION 02_01
 */
/*
 * LAST REVISED 2011-08-15
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
 * IMPORTANT REVISION
 * Revised as a template on 2011-08-15 to produce the same appearance and functionality for day
 * assignments as is present for call assignments.
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
    $page_title = 'Day Assignment Change';



    $datet = new DateTime();
    $datet->setDate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the day is a weekday $_SESSION['weekend']=0, if a weekend or holiday $_SESSION['weekend']=1//
//////////////////////////////////////////////////////////////////////////////////////////////////
* Isn't this SESSION variable already set????????????????????????????????????????????????????????
*/
    /*
     * Section 4
     */
    $_SESSION['weekend'] = isweekend($datet);



    /*
     * Section 7
     */
    echo '<form method="post" action="day_display.php">';

/*
 * Get a list of all possible day assignments
 */

    if (!isset($_SESSION['dayassignment1']) && !isset($_SESSION['dayassignment2']))
    {
        $s0 = "SELECT number
               FROM mds
               WHERE initials = '{$_SESSION['initials']}'";
        $q0 = mysql_query($s0);
        $a0 = mysql_fetch_row($q0);
        $mdno = $a0[0];

        $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
        $dyear = $datetime->format('Y');
        $dm = $datetime->format('n');
        $dd = $datetime->format('j');

        $s1 = "SELECT assignment 
               FROM monthassignment
               WHERE mdnumber = {$mdno}
               AND monthnumber = {$dm}
               AND daynumber = {$dd}
               AND yearnumber = {$dyear}";
        $q1 = mysql_query($s1);
        $a1 = mysql_fetch_row($q1);
        $assig = $a1[0];
   
        if ($assig == 'ORMGR' || 
            $assig == 'ORMGR/Peds' || 
            $_SESSION['initials']=='DB' || 
            $_SESSION['initials']=='PS')
        { 
            $dayAssignmentsListQuery = mysql_query("SELECT DISTINCT assignment, n
                                                    FROM assignments
                                                    WHERE (
                                                              type_number=1
                                                              OR
                                                              type_number=4
                                                              OR
                                                              type_number=5
                                                          )
                                                    AND weekend={$_SESSION['weekend']}
                                                    ORDER BY assignment");
        }
        else
        {
            $dayAssignmentsListQuery = mysql_query("SELECT DISTINCT assignment, n
                                                    FROM assignments
                                                    WHERE (
                                                            type_number=1
                                                            OR
                                                            type_number=4
                                                           )
                                                    AND weekend={$_SESSION['weekend']}
                                                    ORDER BY assignment");
        }
       

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
                <script src="selecttimesday.js">
                </script>
                </head>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="right" width="582">
                            <table align="right" valign="top" width="582" border="0">
                                <td width="182">
                                </td>
                                <td align="right" height="25" width="200px">
                                    Day Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="dayassignment1"
                                onchange="showTimesDay(dayassignment1.value,1)">
             ';

            if (isset($_SESSION['dayassignment1']) && (trim($_SESSION['dayassignment1']) === 'Unwanted Vac'))
            {
                echo "<option value=134 selected='selected'>Unwanted Vac</option>\n";
                $n = 134;
            }
            else if ((isset($_SESSION['dayassignment1']) && trim($_SESSION['dayassignment1']) == 'UwVac'))
            {
                echo "<option value=135 selected='selected'>UwVac</option>\n";
                $n = 135;
            }
            while ($row = @mysql_fetch_row($dayAssignmentsListQuery))
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
                                            <select name="btday1">
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
                                    <select name="etday1">
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
//This allows splitting the day assignment into two segments                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
*
*/
    else
    {
        $s0 = "SELECT number
               FROM mds
               WHERE initials = '{$_SESSION['initials']}'";
        $q0 = mysql_query($s0);
        $a0 = mysql_fetch_row($q0);
        $mdno = $a0[0];

        $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
        $dyear = $datetime->format('Y');
        $dm = $datetime->format('n');
        $dd = $datetime->format('j');

        $s1 = "SELECT assignment 
               FROM monthassignment
               WHERE mdnumber = {$mdno}
               AND monthnumber = {$dm}
               AND daynumber = {$dd}
               AND yearnumber = {$dyear}";
        $q1 = mysql_query($s1);
        $a1 = mysql_fetch_row($q1);
        $assig = $a1[0];
        
        
   
        if ($assig == 'ORMGR' || 
            $assig == 'ORMGR/Peds' || 
            $_SESSION['initials']=='DB' || 
            $_SESSION['initials']=='PS')
        {   
            $result1 = mysql_query("SELECT DISTINCT assignment, n
                                                    FROM assignments
                                                    WHERE (
                                                              type_number=1
                                                              OR
                                                              type_number=4
                                                              OR
                                                              type_number=5
                                                          )
                                                    AND weekend={$_SESSION['weekend']}
                                                    ORDER BY assignment");
        }
        else
        {
            $result1 = mysql_query("SELECT DISTINCT assignment, n
                                                    FROM assignments
                                                    WHERE (
                                                            type_number=1
                                                            OR
                                                            type_number=4
                                                           )
                                                    AND weekend={$_SESSION['weekend']}
                                                    ORDER BY assignment");
        }
        

        day_display_code1($datet);
        
        
        echo '
                <head>
                <script src="selecttimesday.js">
                </script>
                </head>
                <table align="center" border="0" width="100%">
                    <tr>
                        <td align="right" width="582">
                            <table align="right" valign="top" width="582" border="0">
                                <td width="182">
				</td>
                                <td align="right" height="25" width="200px">
                                    Day Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="dayassignment1"
                                onchange="showTimesDay(this.value,1)">
             ';

  
    if (trim($_SESSION['dayassignment1']) == 'Unwanted Vac')
    {
        echo "<option value=134 selected='selected'>Unwanted Vac</option>\n";
        $n = 134;
    }
    else if (trim($_SESSION['dayassignment1']) == 'UwVac')
    {
        echo "<option value=135 selected='selected'>UwVac</option>\n";
        $n = 135;
    }          
    
    while ($result1_array = @mysql_fetch_row($result1))
    {
        if (trim($result1_array[0]) == trim($_SESSION['dayassignment1']))
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
                                            <select name="btday1">
             ';
	  
    $beginTimeTPeriods = getBeginTimeTimePeriods($n);
    while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
    {
        if ($row2[1]==$_SESSION['dayassignment1beginblock'])
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
                                            <select name="etday1">
             ';
	    
    $endTimeTPeriods = getEndTimeTimePeriods($n);
    while ($row3 = @mysql_fetch_row($endTimeTPeriods))
    {
        if ($row3[1]==$_SESSION['dayassignment1endblock'])
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
                                    Day Assignment:
                                </td>
                                <td align="center" height="25" width="200px">
                                <select name="dayassignment2"
                                onchange="showTimesDay(this.value,2)"';
 
                                




/*
 * Section 9
 * Populate the second call option, if $_SESSION['dayassignment2'] is set already, then display
 * it in the option box; otherwise display 'None'
 * If the $_SESSION['trhbt2'] variable is set, it is displayed in the beginning time box for the
 * second call option.
 * If the $_SESSION['trhet2'] variable is set, it is displayed in the ending time box for the
 * second call option.
 */


        
        
        if (!isset ($_SESSION['dayassignment2']))
        {
            $result2Statement = ("SELECT DISTINCT assignment, n, orm FROM assignments WHERE (type_number=1 OR type_number=4) AND weekend={$_SESSION['weekend']} ORDER BY n");                                
            $result2=mysql_query($result2Statement);
            $result2_arr = @mysql_fetch_array($result2);
            var_dump($result2_arr);
            do 
            {                
                    if ($result2_arr['assignment'] != 'None')
                    {
                        echo "<option value={$result2_arr['n']}>{$result2_arr['assignment']}</option>";
                    }
                    else
                    {
                        echo "<option value={$result2_arr['n']} selected='selected'>{$result2_arr['assignment']}</option>";
                        $n = $result2_arr['n'];
                    }
            }
            while ($result2_arr = @mysql_fetch_array($result2));

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
                                        <select name="btday2">
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
                                            <select name="etday2">
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
            $result2Statement = ("SELECT DISTINCT assignment, n FROM assignments WHERE (type_number=1 OR type_number=4) AND weekend={$_SESSION['weekend']} ORDER BY assignment");                                
            $result2=mysql_query($result2Statement);
            $result2_array = @mysql_fetch_row($result2);
            
            var_dump($result2_array);
            do
            {
                if (trim($result2_array[0]) == trim($_SESSION['dayassignment2']))
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
                                        <select name="btday2">
             ';
	  
        $beginTimeTPeriods = getBeginTimeTimePeriods($n);
        while ($row2 = @mysql_fetch_row($beginTimeTPeriods))
        {
            if (trim($row2[1])==trim($_SESSION['dayassignment2beginblock']))
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
                                            <select name="etday2">
                 ';

        $endTimeTPeriods = getEndTimeTimePeriods($n);
        while ($row3 = @mysql_fetch_row($endTimeTPeriods))
        {
            if (trim($row3[1])==trim($_SESSION['dayassignment2endblock']))
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
            <table align="center" width="90%">
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

    $_SESSION['changeassignment']=1;
    include ('includes/footer.html');
}
?>

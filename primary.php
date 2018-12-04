<?php 
session_start();

/*
 * VERSION 02_01
 * Last revised 2011-08-15
*/

/*
 * REVISED 2011-07-04 TO CHANGE THE TIMES TO TIMEPERIODS SO THERE IS NO CONFUSION ABOUT WHICH
 * REPEATING TIMES ARE CORRECT IN THE NEW DAY SCHEME.
*/

/*
 * REVISED 2011-06-16 TO BETTER FORMAT CODE AND REMOVE THE SECONDARY ASSIGNMENT DISPLAY
*/

/*
 * REVISED 2011-06-12 TO UPGRADE FOR THE NEW PAY RULES
*/

/*
 * Revised 2011-07-20 to update the renaming of daydisplay.php to day_display_code1.php and
 * timedisplay.php to day_display_code2.php
 */

/*
 * Revised 2011-08-15 to allow for switching a /PostCall assignment to another /PostCall
 * assignment
 */

/*
 * NOTES:
 * 1.  Does $_SESSION['weekend'] need assignment?  Isn't it already assigned? (Section 4)
 * 2.  By assigning $_SESSION['changeassignment'] to 1 does this pose a problem when the
 *     assignment has not actually been changed? (Section 14)
 * 3.  Can the time blocks as well as the times be communicated between parts of the program as
 *     not to confuse 6 AM at the beginning of the day with 6 AM of the next day? (Sections 9-13)
 */

/*
//Section 1
 *
 */
if (!isset($_SESSION['initials']))
{
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}

/*
//Section 2
 *
 */
else
{
    include ('includes/header.php');
    require_once ($_SESSION['login2string']);

    include ('primaryassign.php');

    include ('day_display_code1.php');
    include ('day_display_code2.php');

    include ('isweekend.php');


/*
//Section 3
//////////////////////////////////////////////////////////////////////////////////////////////////
// COMING TO THE MAJOR PART OF THE PROGRAM AND WHERE IT ORIGINALLY STARTS                       //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    $page_title = 'Primary Assignment Change';



    $datet = new DateTime();
    $datet->setDate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);


/*
//Section 4
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the day is a weekday $_SESSION['dtb']=0, if a weekend or holiday $_SESSION['dtb']=1        //
//////////////////////////////////////////////////////////////////////////////////////////////////
 
    $_SESSION['weekend']=isweekend($datet);
 * 
 */


/*
//Section 5
//////////////////////////////////////////////////////////////////////////////////////////////////
//Begin and end blocks are obtained from table "monthassignment" for existing Primary & Call    //
//Assignments for the current anesthesiologist.                                                 //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    $block3 = "SELECT beginblock, endblock, assignment
               FROM monthassignment
               WHERE yearnumber={$_SESSION['dty']}
               AND monthnumber={$_SESSION['dtm']}
               AND daynumber={$_SESSION['dai']}
               AND assigntype=1
               AND mdnumber=(SELECT number FROM mds
                             WHERE initials='{$_SESSION['schedmd']}')";
    $block2 = mysql_query($block3);
    $block = @mysql_fetch_array($block2);
  
   
    $hblock3 = "SELECT beginblock, endblock
                FROM monthassignment
                WHERE yearnumber={$_SESSION['dty']}
                AND monthnumber={$_SESSION['dtm']}
                AND daynumber={$_SESSION['dai']}
                AND assigntype=3
                AND mdnumber=(SELECT number FROM mds
                              WHERE initials='{$_SESSION['schedmd']}')";
    $hblock2 = mysql_query($hblock3);
    $hblock = @mysql_fetch_row($hblock2);

/*
//Section 6
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
/*
 * Call function daydisplay() which along with timedisplay() (called by daydisplay) displays the
 * first part of the page from the menu bar to the block timeline for this anesthesiologist.
 * These have been renamed as of 2011-07-20 to day_display_code1() and day_display_code2().
 */

    day_display_code1($datet);



/*
//Section 7
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
/* $result1 contains the results from the table "assignments" holding all the primary (or daily)
 * assignments.
 * If someone is assigned the /PostCall assignment these will also show up in case that partner
 * needs to be switched to a different /PostCall assignment
 */


    if (strpos($block['assignment'],'/PostCall')>0)
    {
       $result1=mysql_query("   SELECT DISTINCT assignment,n
                                FROM assignments
                                WHERE addhours=0
                                AND (type_number=1 OR type_number=4 OR type_number=5)
                                AND weekend={$_SESSION['weekend']}
                                ORDER BY assignment");
    }
    else
    {
        $result1=mysql_query("  SELECT DISTINCT assignment,n
                                FROM assignments
                                WHERE addhours=0
                                AND (type_number=1 OR type_number=4)
                                AND weekend={$_SESSION['weekend']}
                                ORDER BY assignment");
    }


/*
//Section 8
//////////////////////////////////////////////////////////////////////////////////////////////////
//Queries for time results for primary assignment                                               //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    if (strpos($block['assignment'],'/PostCall')>0)
    {
        $b3 = "SELECT beginblock, endblock
               FROM assignments
               WHERE weekend={$_SESSION['weekend']}
               AND assignment='{$_SESSION['primaryassignment']}'
               AND (
                      type_number=1
                      OR
                      type_number=4
                      OR
                      type_number=5
                   )
              ";
    }
    else
    {
        $b3 = "SELECT beginblock, endblock
           FROM assignments
           WHERE weekend={$_SESSION['weekend']}
           AND assignment='{$_SESSION['primaryassignment']}'
           AND (
                  type_number=1
                  OR
                  type_number=4
               )
          ";
    }

    $b2 = mysql_query($b3);
    $primaryblocks = @mysql_fetch_row($b2);

/*
 * $tr1 gets the usual blocks from table "assignments" which correspond to the present primary
 * assignment from the beginning to one less than the end since the beginning block cannot be the
 * last block.
 */
   $tr1=mysql_query("SELECT time, timeperiod
                     FROM timeperiods 
                     WHERE timeperiod>=$primaryblocks[0]
                     AND timeperiod<$primaryblocks[1]");

/*
 * $tr2 gets the usual blocks from table "assignments" which correspond to the present primary
 * assignment from one more than the usual beginning block since the end cannot be the first block
 * to the last block.
 */
   $tr2=mysql_query("SELECT time, timeperiod
                     FROM timeperiods
                     WHERE timeperiod>$primaryblocks[0]
                     AND timeperiod<=$primaryblocks[1]");




/*
//Section 9
 *
 */
/*
 * Beginning of the form to get posted back to "day_display.php"
 */
echo '<form method="post" action="day_display.php">';

/*
 * This displays the part of the page below the block timeline with the different possible
 * primary or day assignments along with the possible start and stop times.
 * Javascript to immediately update usual times for when a specific assignment gets choosen.
 */
echo '<head>
      <script src="selecttimes.js">
      </script>
      </head>';
echo '<table align="center" border="0" width="100%">
        <tr>
            <td align="right" width="582">
                <table align="right" width="582" border="0">
                    <tr>
                        <td width="282">
                        </td>
                        <td align="right" height="25" width="200px">
                        Day Assignment:
                        </td>
                        <td align="center" height="25" width="100px">
                        <select name="primaryassignment" onchange="showTimes(this.value)">
       ';

/*
 * $result1 is the query that holds all the primary assignments from the table "assignments"
 * Specifically, those are the assignments which have addedhours=0, callassignment=0, and
 * weekend=$_SESSION['weekend'].
 */
/*
//Section 10
 *
 */
/*
 * $result1 has all the assignments and assignment number (n) for primary assignments
 */
while($row = @mysql_fetch_array($result1))
{ 
    if (trim($row[0])==trim($_SESSION['primaryassignment']))
    {
        echo "<option selected='selected' value=$row[1]>$row[0]</option>\n";
    }
    else
    {
    	echo "<option value=$row[1]>$row[0]</option>\n";
    }
} 
echo '                  </select>
                        </td>
                    </tr>
                </table>
            </td>';



	
echo'	
            <td align="left" width="668">
                <table align="left" border="0">
                    <tr>
                        <td id="txtHint" align="center" height="25px" width="400px">
                            <table border="0">
                                <td align="right" height="25px" width="100px" style="color:black">
                                    Begin Time:
                                </td>
                               <td align="center" height="25px" width="100px" style="color:black">
                                <select name="btprimary">
	  ';

/*
 * This displays the possible start or beginning times of an assignment.
 */
/*
//Section 11
 *
 */
while($row2 = @mysql_fetch_row($tr1))
{
  if (trim($row2[1])==trim($_SESSION['trpbt']))
    echo "                      <option selected='selected' value=$row2[1]>$row2[0]</option>\n";
  else
    echo "                      <option value=$row2[1]>$row2[0]</option>\n";
}

echo '                          </select>
                                </td>
                                <td align="right" height="25" width="100" style="color:black">
                                End Time:
                                </td>
                                <td align="center" height="25" width="100" style="color:black">
                                <select name="etprimary">
	  ';

/*
 * This displays the possible end times of the assignment.
 */
/*
//Section 12
 *
 */
while($row3 = @mysql_fetch_row($tr2))
{
  if (trim($row3[1])==trim($_SESSION['trpet']))
    echo "                      <option selected='selected' value=$row3[1]>$row3[0]</option>\n";
  else
    echo "                      <option value=$row3[1]>$row3[0]</option>\n";
}

echo '                          </select>
                                </td>
                            </table>
                    </tr>
                </table>
            </td>
	</tr>
      </table>';
	  
	  

/*
 * This completes the page with the ending of the form and containing the SUBMIT button.
 */
//Section 13
echo  '
        <table align="center" width="1250px">
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
//////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                              //
//                        END OF PRIMARY ASSIGNMENT DISPLAY CODE                                //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
//Section 14
$_SESSION['changeassignment']=1;
include ('includes/footer.html');
}
?>


<?php
function split2()
{

/*
 * Version 02_01
 * Last Revised 2011-08-09
 */
/*
 * The variables needed here are:
 * (1) assignment number
 * (2) partnerHoldingAssignment
 * (3) partnerGettingAssignment
 */
    /*
     * These all come from split.php
     */

    $getPhysicianNameStatement = "  SELECT first, last
                                    FROM mds
                                    WHERE number={$_SESSION['partnerHoldingAssignment']}";
    $getPhysicianNameQuery = mysql_query($getPhysicianNameStatement);
    $getPhysicianNameResult = @mysql_fetch_array($getPhysicianNameQuery);
    $holdingFirstLast = $getPhysicianNameResult['first'].' '.$getPhysicianNameResult['last'];
    $_SESSION['holdingFirstLast']=$holdingFirstLast;



    $getPhysicianNameStatement2 = " SELECT first, last
                                    FROM mds
                                    WHERE number={$_SESSION['partnerGettingAssignment']}";
    $getPhysicianNameQuery2 = mysql_query($getPhysicianNameStatement2);
    $getPhysicianNameResult2 = @mysql_fetch_array($getPhysicianNameQuery2);
    $gettingFirstLast = $getPhysicianNameResult2['first'].' '.$getPhysicianNameResult2['last'];
    $_SESSION['gettingFirstLast']=$gettingFirstLast;



    $getAssignmentStatement = " SELECT *
                                FROM monthassignment
                                WHERE counter={$_SESSION['assignmentCounter']}";
    $getAssignmentQuery = mysql_query($getAssignmentStatement);
    $getAssignmentResult = @mysql_fetch_array($getAssignmentQuery);
    $assignmentToBeSplit = $getAssignmentResult['assignment'];

    $_SESSION['assignmentToBeSplit']=$assignmentToBeSplit;
    $_SESSION['getAssignmentMonth']=$getAssignmentResult['monthnumber'];
    $_SESSION['getAssignmentDay']=$getAssignmentResult['daynumber'];
    $_SESSION['getAssignmentYear']=$getAssignmentResult['yearnumber'];
    $_SESSION['assignType']=$getAssignmentResult['assigntype'];
    $_SESSION['mdNumberHolding']=$getAssignmentResult['mdnumber'];





    switch ($getAssignmentResult['monthnumber'])
    {
        case '1':
            $month = 'January';
            break;
        case '2':
            $month = 'February';
            break;
        case '3':
            $month = 'March';
            break;
        case '4':
            $month = 'April';
            break;
        case '5':
            $month = 'May';
            break;
        case '6':
            $month = 'June';
            break;
        case '7':
            $month = 'July';
            break;
        case '8':
            $month = 'August';
            break;
        case '9':
            $month = 'September';
            break;
        case '10':
            $month = 'October';
            break;
        case '11':
            $month = 'November';
            break;
        case '12':
            $month = 'December';
            break;
    }

    echo '  <table style="width:100%; align:center">
                <tr>
                    <td width="25%">
                    </td>
                    <td width="30%">
                        Partner with Assignment to Split:
                    </td>
                    <td width="20%" align="center">'.
                    $holdingFirstLast.'
                    </td>
                    <td width="25%">
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                    </td>
                    <td width="30%">
                        Partner Getting Portion of the Split Assignment:
                    </td>
                    <td width="20%" align="center">'.
                    $gettingFirstLast.'
                    </td>
                    <td width="25%">
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                    </td>
                    <td width="30%">
                        Assignment Being Split:
                    </td>
                    <td width="20%" align="center">'.
                    $assignmentToBeSplit.'
                    </td>
                    <td width="25%">
                    </td>
                </tr>
                <tr>
                    <td width="25%">
                    </td>
                    <td width="30%">
                        Date of Assignment:
                    </td>
                    <td width="20%" align="center">'.
                    $getAssignmentResult['yearnumber'].'-'.$month.'-'.
                    $getAssignmentResult['daynumber'].'
                    </td>
                    <td width="25%">
                    </td>
                </tr>
         </table>';











      $firstSplitBeginTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod={$getAssignmentResult['beginblock']}");

      $firstSplitEndTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod>{$getAssignmentResult['beginblock']}
            AND timeperiod<={$getAssignmentResult['endblock']}");


    echo '<form method="post" action="split3.php">';
    echo '<br><br><br>
        <table align="center" width="100%">
            <tr>
            <td align="right" width="35%">
                <table align="right" width="100%">
                    <tr>
                        <td align="center" height="25" width="60%" style="color:black">
                        First Part of Assignment:
                        </td>
                        <td align="center" height="25" width="40%">
                        <select name="firstSplitMD">
       ';


    	echo "<option value={$_SESSION['partnerHoldingAssignment']}>$holdingFirstLast</option>\n";
        echo "<option value={$_SESSION['partnerGettingAssignment']}>$gettingFirstLast</option>\n";

        echo '          </select>
                        </td>
                    </tr>
                </table>
            </td>';




        echo'
            <td align="left" width="65%">
                <table align="left" width="100%">
                    <tr>
                        <td align="center" height="25px">
                            <table  width="100%">
                                <td align="right" height="25px" width="25%" style="color:black">
                                    Begin Time:
                                </td>
                                <td align="center" height="25px" width="25%" style="color:black">
                                <select name="firstSplitBeginTimePeriod">
	  ';

while($firstSplitBT = @mysql_fetch_array($firstSplitBeginTimePeriods))
{
  if (trim($firstSplitBT['timeperiod'])==trim($getAssignmentResult['beginblock']))
    echo "              <option selected='selected' value={$firstSplitBT['timeperiod']}>
                            {$firstSplitBT['time']}</option>\n";
  else
    echo "              <option value={$firstSplitBT['timeperiod']}>
                            {$firstSplitBT['time']}</option>\n";
}

echo '                          </select>
                                </td>
                                <td align="right" height="25" width="25%" style="color:black">
                                End Time:
                                </td>
                                <td align="center" height="25" width="25%" style="color:black">
                                <select name="firstSplitEndTimePeriod">
	  ';

while($firstSplitET = @mysql_fetch_array($firstSplitEndTimePeriods))
{
  if (trim($firstSplitET['timeperiod'])==trim($getAssignmentResult['endblock']))
    echo "             <option selected='selected' value={$firstSplitET['timeperiod']}>
                            {$firstSplitET['time']}</option>\n";
  else
    echo "             <option value={$firstSplitET['timeperiod']}>
                            {$firstSplitET['time']}</option>\n";
}

        echo '                  </select>
                                </td>
                            </table>
                    </tr>
                </table>
            </td>
	</tr>
      </table>';








      $secondSplitBeginTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod>={$getAssignmentResult['beginblock']}
            AND timeperiod<{$getAssignmentResult['endblock']}");

      $secondSplitEndTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod>{$getAssignmentResult['beginblock']}
            AND timeperiod<={$getAssignmentResult['endblock']}");


    echo '<br>
        <table align="center" width="100%">
            <tr>
            <td align="right" width="35%">
                <table align="right" width="100%">
                    <tr>
                        <td align="center" height="25" width="60%" style="color:black">
                        Second Part of Assignment:
                        </td>
                        <td align="center" height="25" width="40%">
                        <select name="secondSplitMD">
       ';


    	echo "<option value={$_SESSION['partnerHoldingAssignment']}>$holdingFirstLast</option>\n";
        echo "<option value={$_SESSION['partnerGettingAssignment']}>$gettingFirstLast</option>\n";

        echo '          </select>
                        </td>
                    </tr>
                </table>
            </td>';




        echo'
            <td align="left" width="65%">
                <table align="left" width="100%">
                    <tr>
                        <td align="center" height="25px">
                            <table  width="100%">
                                <td align="right" height="25px" width="25%" style="color:black">
                                    Begin Time:
                                </td>
                                <td align="center" height="25px" width="25%" style="color:black">
                                <select name="secondSplitBeginTimePeriod">
	  ';

while($secondSplitBT = @mysql_fetch_array($secondSplitBeginTimePeriods))
{
  if (trim($secondSplitBT['timeperiod'])==trim($getAssignmentResult['beginblock']))
    echo "              <option selected='selected' value={$secondSplitBT['timeperiod']}>
                            {$secondSplitBT['time']}</option>\n";
  else
    echo "              <option value={$secondSplitBT['timeperiod']}>
                            {$secondSplitBT['time']}</option>\n";
}

echo '                          </select>
                                </td>
                                <td align="right" height="25" width="25%" style="color:black">
                                End Time:
                                </td>
                                <td align="center" height="25" width="25%" style="color:black">
                                <select name="secondSplitEndTimePeriod">
	  ';

while($secondSplitET = @mysql_fetch_array($secondSplitEndTimePeriods))
{
  if (trim($secondSplitET['timeperiod'])==trim($getAssignmentResult['endblock']))
    echo "             <option selected='selected' value={$secondSplitET['timeperiod']}>
                            {$secondSplitET['time']}</option>\n";
  else
    echo "             <option value={$secondSplitET['timeperiod']}>
                            {$secondSplitET['time']}</option>\n";
}

        echo '                  </select>
                                </td>
                            </table>
                    </tr>
                </table>
            </td>
	</tr>
      </table>';









      $thirdSplitBeginTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod>={$getAssignmentResult['beginblock']}
            AND timeperiod<{$getAssignmentResult['endblock']}");

      $thirdSplitEndTimePeriods=mysql_query
          ("SELECT time, timeperiod
            FROM timeperiods
            WHERE timeperiod={$getAssignmentResult['endblock']}");


    echo '<br>
        <table align="center" width="100%">
            <tr>
            <td align="right" width="35%">
                <table align="right" width="100%">
                    <tr>
                        <td align="center" height="25" width="60%" style="color:black">
                        Third Part of Assignment:
                        </td>
                        <td align="center" height="25" width="40%">
                        <select name="thirdSplitMD">
       ';


    	echo "<option value={$_SESSION['partnerHoldingAssignment']}>$holdingFirstLast</option>\n";
        echo "<option value={$_SESSION['partnerGettingAssignment']}>$gettingFirstLast</option>\n";
        echo "<option selected='selected' value=Unnecessary>Unnecessary</option>\n";

        echo '          </select>
                        </td>
                    </tr>
                </table>
            </td>';




        echo'
            <td align="left" width="65%">
                <table align="left" width="100%">
                    <tr>
                        <td align="center" height="25px">
                            <table  width="100%">
                                <td align="right" height="25px" width="25%" style="color:black">
                                    Begin Time:
                                </td>
                                <td align="center" height="25px" width="25%" style="color:black">
                                <select name="thirdSplitBeginTimePeriod">
	  ';

while($thirdSplitBT = @mysql_fetch_array($thirdSplitBeginTimePeriods))
{
  if (trim($thirdSplitBT['timeperiod'])==trim($getAssignmentResult['beginblock']))
    echo "              <option selected='selected' value={$thirdSplitBT['timeperiod']}>
                            {$thirdSplitBT['time']}</option>\n";
  else
    echo "              <option value={$thirdSplitBT['timeperiod']}>
                            {$thirdSplitBT['time']}</option>\n";
}

echo '                          </select>
                                </td>
                                <td align="right" height="25" width="25%" style="color:black">
                                End Time:
                                </td>
                                <td align="center" height="25" width="25%" style="color:black">
                                <select name="thirdSplitEndTimePeriod">
	  ';

while($thirdSplitET = @mysql_fetch_array($thirdSplitEndTimePeriods))
{
  if (trim($thirdSplitET['timeperiod'])==trim($getAssignmentResult['endblock']))
    echo "             <option selected='selected' value={$thirdSplitET['timeperiod']}>
                            {$thirdSplitET['time']}</option>\n";
  else
    echo "             <option value={$thirdSplitET['timeperiod']}>
                            {$thirdSplitET['time']}</option>\n";
}

        echo '                  </select>
                                </td>
                            </table>
                    </tr>
                </table>
            </td>
	</tr>
      </table>
      <br><br>
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
      </table>
      </form>';


    
    
    //unset($_SESSION['partnerHoldingAssignment']);
    //unset($_SESSION['partnerGettingAssignment']);
    //unset($_SESSION['assignmentCounter']);
    unset($_SESSION['docs']);
}
?>

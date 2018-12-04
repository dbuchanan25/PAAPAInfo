<?php
session_start();
/*
 * Version 02_01
 * Last Revised 2011-10-02
 */
/*
 * Revised 2011-10-02 to correct the monthcal entry when the third time period is 'Unnecessary'
 * and to set the conflict blocks correctly.
 */
/*
 * $_REQUEST variables
 * firstSplitMD - MD number
 * firstSplitBeginTimePeriod
 * firstSplitEndTimePeriod
 * secondSplitMD - MD number
 * secondSplitBeginTimePeriod
 * secondSplitEndTimePeriod
 * thirdSplitMD - MD number
 * thirdSplitBeginTimePeriod
 * thirdSplitEndTimePeriod
 */
 /*
  * $_SESSION['holdingFirstLast']
  * $_SESSION['gettingFirstLast']
  * $_SESSION['assignmentToBeSplit']
  * $_SESSION['getAssignmentMonth']
  * $_SESSION['getAssignmentDay']
  * $_SESSION['getAssignmentYear']
  * $_SESSION['assignmentCounter'] - the index number (counter) of the assignment being split
  * $_SESSION['assignType']
  * $_SESSION['mdNumberHolding']
  * $_SESSION['partnerHoldingAssignment'] - mdnumber
  * $_SESSION['partnerGettingAssignment'] - mdnumber
  */

/*
 * Check times to make sure they are correct..
 */

include ('includes/header.html');
require_once ($_SESSION['login2string']);


$assignmentInfoStatement = "SELECT *
                            FROM monthassignment
                            WHERE counter={$_SESSION['assignmentCounter']}";
$assignmentInfoQuery = mysql_query($assignmentInfoStatement);
$assignmentInfoResult = mysql_fetch_array($assignmentInfoQuery);

$workingPrimary=false;
$conflict=0;
$blocks[100]=0;
$checkGettingBlocksStatement =
    "SELECT beginblock, endblock, assignment
     FROM monthassignment
     WHERE mdnumber = {$_SESSION['partnerGettingAssignment']}
     AND monthnumber = {$_SESSION['getAssignmentMonth']}
     AND daynumber = {$_SESSION['getAssignmentDay']}
     AND yearnumber = {$_SESSION['getAssignmentYear']}";
$checkGettingBlocksQuery = mysql_query($checkGettingBlocksStatement);
while($checkGettingBlocks = mysql_fetch_array($checkGettingBlocksQuery))
{
    $checkAssignmentTypeStatement = "SELECT type_number
                                     FROM assignments
                                     WHERE assignment LIKE '{$checkGettingBlocks['assignment']}%'
                                     AND weekend={$assignmentInfoResult['weekend']}";
    $checkAssignmentTypeQuery = mysql_query($checkAssignmentTypeStatement);
    $checkAssignmentTypeResult = mysql_fetch_array($checkAssignmentTypeQuery);
    
    if ($checkAssignmentTypeResult[0]!=4 && trim($checkGettingBlocks['assignment'])!='None')
    {
        for ($x = $checkGettingBlocks['beginblock']; $x < $checkGettingBlocks['endblock']; $x++)
        {
            $blocks[$x]=1;
        }
        $workingPrimary=true;
    }
}


/*
 * Check for all possible conflicts.
 * * Whether the "Getting" partner already has an assignment at the times he is supposed to be
 *   getting the split assignment.
 * * Whether the times as far as first end time equaling the second begin time are in place.
 * * Whether the first MD is the same as the third MD (or that the third portion of the split is
 *   unnecessary).
 */
if ($_SESSION['partnerGettingAssignment']==$_REQUEST['firstSplitMD'])
{
    for ($x=$_REQUEST['firstSplitBeginTimePeriod']; $x<$_REQUEST['firstSplitEndTimePeriod'];
                $x++)
    {
        if ($blocks[$x]==1)
        {
            $conflict=1;
        }
    }
}

if ($_SESSION['partnerGettingAssignment']==$_REQUEST['secondSplitMD'])
{
    for ($x=$_REQUEST['secondSplitBeginTimePeriod']; $x<$_REQUEST['secondSplitEndTimePeriod'];
                $x++)
    {
        if ($blocks[$x]==1)
        {
            $conflict=2;
        }
    }
}

if ($_SESSION['partnerGettingAssignment']==$_REQUEST['thirdSplitMD'])
{
    for ($x=$_REQUEST['thirdSplitBeginTimePeriod']; $x<$_REQUEST['thirdSplitEndTimePeriod'];
                $x++)
    {
        if ($blocks[$x]==1)
        {
            $conflict=3;
        }
    }
}

if (
        $_REQUEST['firstSplitEndTimePeriod']!=$_REQUEST['secondSplitBeginTimePeriod']
        ||
        (
            $_REQUEST['secondSplitEndTimePeriod']!=$_REQUEST['thirdSplitBeginTimePeriod']
            &&
            $_REQUEST['thirdSplitMD']!='Unnecessary'
        )
        ||
        (
            $_REQUEST['firstSplitMD']!=$_REQUEST['thirdSplitMD']
            &&
            $_REQUEST['thirdSplitMD']!='Unnecessary'
        )
        ||
        $_REQUEST['firstSplitMD']==$_REQUEST['secondSplitMD']
        ||
        $_REQUEST['secondSplitMD']==$_REQUEST['thirdSplitMD']
   )
{
    $conflict=4;
}

/*
 * If a conflict results, unset all $_SESSION variables and send back to "day_display.php"
 */
if ($conflict==1 || $conflict==2 || $conflict==3 || $conflict==4)
{
        echo '  <h2>
                <center>
                There has been a mistake in the entered data.
                </center>
                </h2>
                <br>
                <center>
                You need to try again.  If this is a continuing issue please report<br>
                the number '.$conflict.' to Dale Buchanan.<br>
                </center>
                <br><br>';
        unset($_SESSION['partnerHoldingAssignment']);
        unset($_SESSION['partnerGettingAssignment']);
        unset($_SESSION['assignmentCounter']);
        unset($_SESSION['docs']);
        unset($_SESSION['holdingFirstLast']);
        unset($_SESSION['gettingFirstLast']);
        unset($_SESSION['assignmentToBeSplit']);
        unset($_SESSION['getAssignmentMonth']);
        unset($_SESSION['getAssignmentDay']);
        unset($_SESSION['getAssignmentYear']);
}
/*
 * If no conflict -
 * * Insert into table bumonthassignment the original assignment
 * * Delete the original assignment from table monthassignment and table monthcal
 * * Insert the new 'first split' assignment into table monthassignment and table monthcal
 * * Insert the new 'second split' assignment into table monthassignment and table monthcal
 * * Insert the new 'third split' assignment into table monthassignment and table monthcal if
 *   necessary
 */
else
{
    $firstThirdHours = 0;
    $normalHours = ($assignmentInfoResult['endblock']-$assignmentInfoResult['beginblock'])/4;



    $getTimesStatement = "  SELECT time
                            FROM timeperiods
                            WHERE timeperiod={$_REQUEST['firstSplitEndTimePeriod']}";
    $getTimesQuery = mysql_query($getTimesStatement);
    $getTimesResult = mysql_fetch_array($getTimesQuery);
    $firstEndTime = $getTimesResult['time'];
    $getTimesStatement = "  SELECT time
                            FROM timeperiods
                            WHERE timeperiod={$_REQUEST['secondSplitEndTimePeriod']}";
    $getTimesQuery = mysql_query($getTimesStatement);
    $getTimesResult = mysql_fetch_array($getTimesQuery);
    $secondEndTime = $getTimesResult['time'];


    $deleteMonthCalStatement = "    DELETE
                                    FROM monthcal
                                    WHERE mdnumber={$_SESSION['mdNumberHolding']}
                                    AND monthnumber={$_SESSION['getAssignmentMonth']}
                                    AND daynumber={$_SESSION['getAssignmentDay']}
                                    AND yearnumber={$_SESSION['getAssignmentYear']}
                                    AND assignment LIKE '%{$_SESSION['assignmentToBeSplit']}%'";
    mysql_query($deleteMonthCalStatement);



    
    if ($workingPrimary==false)
    {
        $deleteGettingMonthAssignmentStatement =
           "DELETE
            FROM monthassignment
            WHERE mdnumber = {$_SESSION['partnerGettingAssignment']}
            AND monthnumber = {$_SESSION['getAssignmentMonth']}
            AND daynumber = {$_SESSION['getAssignmentDay']}
            AND yearnumber = {$_SESSION['getAssignmentYear']}
            AND assigntype = {$assignmentInfoResult['assigntype']}";
        mysql_query($deleteGettingMonthAssignmentStatement);
    }




    $deleteMonthAssignmentStatement = " DELETE
                                        FROM monthassignment
                                        WHERE counter={$_SESSION['assignmentCounter']}";
    mysql_query($deleteMonthAssignmentStatement);




    $insertFirstMonthAssignmentStatement = "INSERT INTO monthassignment
                                            VALUES (
                                                    {$_REQUEST['firstSplitMD']},
                                                    {$_SESSION['getAssignmentMonth']},
                                                    {$_SESSION['getAssignmentDay']},
                                                    {$_SESSION['getAssignmentYear']},
                                                    '{$assignmentInfoResult['assignment']}',
                                                    {$assignmentInfoResult['assigntype']},
                                                    '{$assignmentInfoResult['bt']}',
                                                    {$assignmentInfoResult['beginblock']},
                                                    '$firstEndTime',
                                                    {$_REQUEST['firstSplitEndTimePeriod']},
                                                    {$assignmentInfoResult['weekend']},
                                                    NULL,
                                                    {$_SESSION['mdn']},
                                                    NULL
                                                   )";
    mysql_query($insertFirstMonthAssignmentStatement);
    $firstThirdHours = ($_REQUEST['firstSplitEndTimePeriod']-$assignmentInfoResult['beginblock'])
                        /4;



    $insertSecondMonthAssignmentStatement ="INSERT INTO monthassignment
                                            VALUES (
                                                    {$_REQUEST['secondSplitMD']},
                                                    {$_SESSION['getAssignmentMonth']},
                                                    {$_SESSION['getAssignmentDay']},
                                                    {$_SESSION['getAssignmentYear']},
                                                    '{$assignmentInfoResult['assignment']}',
                                                    {$assignmentInfoResult['assigntype']},
                                                    '$firstEndTime',
                                                    {$_REQUEST['firstSplitEndTimePeriod']},
                                                    '$secondEndTime',
                                                    {$_REQUEST['secondSplitEndTimePeriod']},
                                                    {$assignmentInfoResult['weekend']},
                                                    NULL,
                                                    {$_SESSION['mdn']},
                                                    NULL
                                                   )";
    mysql_query($insertSecondMonthAssignmentStatement);

    $secondHours = ($_REQUEST['secondSplitEndTimePeriod']-$_REQUEST['firstSplitEndTimePeriod'])/4;
    $secondTimeDiff = $secondHours-$normalHours;
    $insertSecondMonthCalStatement = "      INSERT INTO monthcal
                                            VALUES (
                                                    {$_REQUEST['secondSplitMD']},
                                                    {$_SESSION['getAssignmentMonth']},
                                                    {$_SESSION['getAssignmentDay']},
                                                    {$_SESSION['getAssignmentYear']},
                                                    '{$assignmentInfoResult['assignment']}',
                                                    {$assignmentInfoResult['assigntype']},
                                                    {$assignmentInfoResult['weekend']},
                                                    $secondTimeDiff
                                                   )";
    mysql_query($insertSecondMonthCalStatement);

    if ($_REQUEST['thirdSplitMD'] != 'Unnecessary')
    {
        $insertThirdMonthAssignmentStatement =" INSERT INTO monthassignment
                                                VALUES (
                                                        {$_REQUEST['thirdSplitMD']},
                                                        {$_SESSION['getAssignmentMonth']},
                                                        {$_SESSION['getAssignmentDay']},
                                                        {$_SESSION['getAssignmentYear']},
                                                        '{$assignmentInfoResult['assignment']}',
                                                        {$assignmentInfoResult['assigntype']},
                                                        '$secondEndTime',
                                                        {$_REQUEST['secondSplitEndTimePeriod']},
                                                        '{$assignmentInfoResult['et']}',
                                                        {$assignmentInfoResult['endblock']},
                                                        {$assignmentInfoResult['weekend']},
                                                        NULL,
                                                        {$_SESSION['mdn']},
                                                        NULL
                                                       )";
        mysql_query($insertThirdMonthAssignmentStatement);

        $thirdHours = ($assignmentInfoResult['endblock']-$_REQUEST['secondSplitEndTimePeriod'])/4;
        $firstThirdTimeDiff = ($firstThirdHours+$thirdHours)-$normalHours;
        $insertThirdMonthCalStatement = "   INSERT INTO monthcal
                                            VALUES (
                                                    {$_REQUEST['thirdSplitMD']},
                                                    {$_SESSION['getAssignmentMonth']},
                                                    {$_SESSION['getAssignmentDay']},
                                                    {$_SESSION['getAssignmentYear']},
                                                    '{$assignmentInfoResult['assignment']}',
                                                    {$assignmentInfoResult['assigntype']},
                                                    {$assignmentInfoResult['weekend']},
                                                    $firstThirdTimeDiff
                                                   )";
        mysql_query($insertThirdMonthCalStatement);
    }
    else
    {
        $firstThirdTimeDiff = $firstThirdHours-$normalHours;
        $insertFirstMonthCalStatement = "   INSERT INTO monthcal
                                            VALUES (
                                                    {$_REQUEST['firstSplitMD']},
                                                    {$_SESSION['getAssignmentMonth']},
                                                    {$_SESSION['getAssignmentDay']},
                                                    {$_SESSION['getAssignmentYear']},
                                                    '{$assignmentInfoResult['assignment']}',
                                                    {$assignmentInfoResult['assigntype']},
                                                    {$assignmentInfoResult['weekend']},
                                                    $firstThirdTimeDiff
                                                   )";
        mysql_query($insertFirstMonthCalStatement);
    }
    echo '
            <center>
            <h2>
            Assignment has been split successfully.
            </h2>
            </center>
            <br><br>';
}


   echo '<center>
              <FORM METHOD="LINK" ACTION="day_display.php">
              <INPUT TYPE="submit" VALUE="Submit">
              </FORM>
              </center>';
   echo '<br><br>';
   include ('includes/footer.html');
?>

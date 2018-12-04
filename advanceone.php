<?php
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-07
 */
if (!isset($_SESSION)) { session_start(); }


if (!isset($_SESSION['initials']))
{
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}


else
{
    require_once ($_SESSION['login2string']);

    echo'
        <body><center>';
    /*
	<div class="menu">
	<table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#E5E5E5">
            <tr align="center">
                <td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="choose.php">Schedule For Page</a></td>
		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="monthcalendar2.php">Complete Month</a></td>
		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="printFriendlyPlusTwo.php" target="_blank">Print Friendly Version</a></td>
		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="advanceone.php" target="_blank">Advance Two Days</a></td>
                <td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="unwanted.php">Assign Unscheduled Vacation</a></td>
                <td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="unwantedlist.php">Unscheduled Vacation List</a></td>
   		<td align="center" width="10%" height="1%" bordercolor="#808080">
                    <a href="logout.php">Logout</a></td>
            </tr>
        </table>
        </div>';
     * 
     */

    
    include_once 'menuBar.php';
    menuBar(1567);
    
    
    
    echo '
	<center><h5>OR Manager Worksheet</h5><br><br>';

	$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
        $datetime->modify("+2 days");


    /*
     * Set up the table to display the results.  Create a row of dates across the top.
     */
    echo '
	<table class="table2">
            <tr>
                <th>Assignment
                </th>
                <th>'.$datetime->format('D').', '.
                    $datetime->format('M').' '.$datetime->format('j').'
                </th>';

          $datetime->modify("+1 day");
    echo '
                <th>'.$datetime->format('D').', '.
                        $datetime->format('M').' '.$datetime->format('j').'
                </th>';

	  $datetime->modify("+1 day");
    echo '
                <th>'.$datetime->format('D').
                    ', '.$datetime->format('M').' '.$datetime->format('j').'
                </th>';
	  $datetime->modify("+1 day");
    echo '
                <th>'.$datetime->format('D').
                    ', '.$datetime->format('M').' '.$datetime->format('j').'
                </th>
            </tr>';

    /*
     * Reset the date back three days
     */
    $datetime->modify("-3 days");

    for ($day=0; $day<4; $day++)
    {
        $getAssignmentsStatement = "SELECT DISTINCT orm, assignment
                                    FROM assignments
                                    WHERE orm IS NOT NULL
                                    ORDER BY orm, assignment";
	$getAssignmentsQuery = mysql_query($getAssignmentsStatement);
        while ($getAssignmentsResult = @mysql_fetch_array($getAssignmentsQuery))
        {
            if ($getAssignmentsResult['orm']!=NULL)
            {
                $sqlday1 = "SELECT mdnumber
                            FROM monthassignment
                            WHERE monthnumber={$datetime->format('m')}
                            AND daynumber={$datetime->format('j')}
                            AND yearnumber={$datetime->format('Y')}
                            AND assignment='{$getAssignmentsResult['assignment']}'";
                $sqlday1q = mysql_query($sqlday1);
                $sqlday1a = @mysql_fetch_array($sqlday1q);
                if (!empty($sqlday1a['mdnumber']))
                {
                    $sqlmdnames = " SELECT last
                                    FROM mds
                                    WHERE number={$sqlday1a['mdnumber']}";
                    $sqlmdnameq = mysql_query($sqlmdnames);
                    $sqlmdname = @mysql_fetch_array($sqlmdnameq);
                    $chart[$getAssignmentsResult['assignment']][$day]['number']=
                                $sqlday1a['mdnumber'];
                    $chart[$getAssignmentsResult['assignment']][$day]['name']=
                                $sqlmdname['last'];
                }
            }
        }
        $datetime->modify("+1 day");
    }

    $getAssignmentsStatement = "    SELECT DISTINCT orm, assignment
                                    FROM assignments
                                    WHERE orm IS NOT NULL
                                    ORDER BY orm, assignment";
    $getAssignmentsQuery = mysql_query($getAssignmentsStatement);

    while ($getAssignmentsResult = @mysql_fetch_array($getAssignmentsQuery))
    {
        if (
            empty($chart[$getAssignmentsResult['assignment']][0]['number'])
            &&
            empty($chart[$getAssignmentsResult['assignment']][1]['number'])
            &&
            empty($chart[$getAssignmentsResult['assignment']][2]['number'])
            &&
            empty($chart[$getAssignmentsResult['assignment']][3]['number'])
           )
        {}
        else
        {
            if ($getAssignmentsResult['orm']==64)
            {
                echo '
                <tr>
                    <td width="20%" height="1%" align="center" style="font-weight:bold">
                        H 1 Call
                    </td>';
            }
            else if ($getAssignmentsResult['orm']==76)
            {
                echo '
                <tr>
                    <td width="20%" height="1%" align="center" style="font-weight:bold">
                        C OH Call
                    </td>';
            }
            else
            {

                echo '
                    <tr>
                        <td width="20%" height="1%" align="center" style="font-weight:bold">
                        '
                            .$getAssignmentsResult['assignment'].
                        '
                        </td>';
            }
            
                    if (!empty($chart[$getAssignmentsResult['assignment']][0]['name']))
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        '
                            .$chart[$getAssignmentsResult['assignment']][0]['name'].' ('
                            .$chart[$getAssignmentsResult['assignment']][0]['number'].')
                        </td>';
                    }
                    else
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        </td>';
                    }

                    if (!empty($chart[$getAssignmentsResult['assignment']][1]['name']))
                    {
                        echo'
                        <td width="20%" height="1%" align="center" bgcolor="yellow">
                        '
                            .$chart[$getAssignmentsResult['assignment']][1]['name'].' ('
                            .$chart[$getAssignmentsResult['assignment']][1]['number'].')
                        </td>';
                    }
                    else
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        </td>';
                    }

                    if (!empty($chart[$getAssignmentsResult['assignment']][2]['name']))
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        '
                            .$chart[$getAssignmentsResult['assignment']][2]['name'].' ('
                            .$chart[$getAssignmentsResult['assignment']][2]['number'].')
                        </td>';
                    }
                    else
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        </td>';
                    }
                    if (!empty($chart[$getAssignmentsResult['assignment']][3]['name']))
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        '
                            .$chart[$getAssignmentsResult['assignment']][3]['name'].' ('
                            .$chart[$getAssignmentsResult['assignment']][3]['number'].')
                        </td>';
                    }
                    else
                    {
                        echo'
                        <td width="20%" height="1%" align="center">
                        </td>';
                    }
                echo'
                </tr>';
        }
    }
    echo '</table>';
    echo '<table class="table2">';
    
    
    $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
    $year = $datetime->format('Y');
    $month = $datetime->format('m');
    $day = $datetime->format('d');


    $meetingStatement = "SELECT *
                         FROM meetings
                         WHERE yearnumber=$year
                         AND monthnumber=$month
                         AND daynumber>=$day
                         AND daynumber<=($day+3)
                         ORDER BY daynumber";
    $meetingQuery = mysql_query($meetingStatement);

    echo'
	    <tr>
                <td width="100%" height="1%">Meetings:';
                if (mysql_num_rows($meetingQuery)==0)
                    echo "--NO MEETINGS FOR THESE DAYS--";
                else
                {
                    while ($meeting = @mysql_fetch_array($meetingQuery))
                    {
                        $btTextStatement = "    SELECT time
                                                FROM timeperiods
                                                WHERE timeperiod={$meeting['begintimeperiod']}";
                        $etTextStatement = "    SELECT time
                                                FROM timeperiods
                                                WHERE timeperiod={$meeting['endtimeperiod']}";
                        $btTextQuery = mysql_query($btTextStatement);
                        $btText = mysql_fetch_array($btTextQuery);
                        $etTextQuery = mysql_query($etTextStatement);
                        $etText = mysql_fetch_array($etTextQuery);
                        echo "--".$meeting['mdinitials']."/Day:".$meeting['daynumber']."/Begin:".
                            substr($btText[0],0,5)."/End:".substr($etText[0],0,5).
                            "/".$meeting['commnt']."--";
                     }
                 }
                 echo'
                </td>
            </tr>
        </table>';
}

?>

<?php
    
require_once ('connect2.php');

    
    echo'
    <link rel="stylesheet" href="styleORM.css" type="text/css">
    ';
    
    echo '<title>ORMGR Page</title>';
    
    /*$mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'ormpage.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
     * 
     */

    echo'
        <body><center>';
    
    
    
    include_once 'menuBarORM.php';
    menuBar(3711);
    
    
    
    
    echo '
	<center><h2>OR Manager Worksheet</h2><br><br>';

    $month = $_POST['month'];
    $day = $_POST['day'];
    $year = $_POST['year'];
    
    $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    if ($day > $number)
    {
        echo'
            <form method="post" action="ormpre.php" class="input">
            <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display=\'none\';"></span> 
            <strong>Error!</strong> The date does not exist. There are not that many days in that month.  Press SUBMIT to start over.
            </div>
            <br><br><br>
        
        <table align="center" width="100%" style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px; border:none">
            <tr>
                <td width="50%" align="center" style="border: none">
                     <input type="submit" name="ORMA" value="SUBMIT" class="btn">
                </td>
                </form>
            </tr>
        </table>';

    }
    else 
    {
    
        $datetime = new DateTime('now');
    
        if (isset($month) && isset($day) && isset($year))
            $datetime->setDate($year, $month, $day);
        else 
        {
            $datetime->modify(("+1 days"));
        }


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
                    $sqlmdnames = " SELECT last, first
                                    FROM mds
                                    WHERE number={$sqlday1a['mdnumber']}";
                    $sqlmdnameq = mysql_query($sqlmdnames);
                    $sqlmdname = @mysql_fetch_array($sqlmdnameq);
                    $fullname = $sqlmdname['last'].', '.$sqlmdname['first'];
                    $chart[$getAssignmentsResult['assignment']][$day]['number']=
                                $sqlday1a['mdnumber'];
                    $chart[$getAssignmentsResult['assignment']][$day]['name']=
                                $fullname;
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
                            .$chart[$getAssignmentsResult['assignment']][0]['name'];
                                //.  ' ('
                        //    .$chart[$getAssignmentsResult['assignment']][0]['number'].')
                    echo'    </td>';
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
                            .$chart[$getAssignmentsResult['assignment']][1]['name'];
                        //    .$chart[$getAssignmentsResult['assignment']][1]['number'].')
                        echo'        
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
                            .$chart[$getAssignmentsResult['assignment']][2]['name'];
                        //    .$chart[$getAssignmentsResult['assignment']][2]['number'].')
                        echo'
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
                            .$chart[$getAssignmentsResult['assignment']][3]['name'];
                        //    .$chart[$getAssignmentsResult['assignment']][3]['number'].')
                        echo'
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
    $datetime = new DateTime('now', new DateTimeZone('US/Eastern'));    
    $datetimestart = new DateTime('now', new DateTimeZone('US/Eastern'));

    
    $year = $datetime->format('Y');
    $month = $datetime->format('m');
    $day = $datetime->format('d');
   
    $datetimeplus = date_add($datetime,date_interval_create_from_date_string("3 days"));

    
    $meetingStatement =  "SELECT * ".
                         "FROM meetings ".
                         "WHERE meetingdate >= '".
                         date_format($datetimestart, "Y-m-d").
                         "' AND meetingdate <= '".
                         date_format($datetimeplus, "Y-m-d")."' ORDER BY meetingdate";
    
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
        </table>';
    }
?>

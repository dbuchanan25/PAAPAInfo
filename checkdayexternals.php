<?php
/*
 * Version 02_02
 */
/*
 * Last Revised 2013-05-18
 */
function checkdayexternals()
{

    /*
     * Revision on 2011-07-16 and 17 updates for the $blocks variable to have 100 elements,
     * better documentation of the $_SESSION['check'] variable, clearer display of the error
     * message, better documentation of the return value meaning, commenting out the $blocks[]
     * check for conflict when only the switching partner's assignment is being evaluated.
     */
    /*
    //REVISED 2011-04-02 ADDING $_SESSION['check'] VARIABLE SO ERROR DOESN'T PRINT TWICE
    //AND FORMATTING TO MORE EASILY PRINT
    //REVISED 2011-06-24 TO BETTER FORMAT THE CODE IN PREPARATION OF INSTITUTION THE NEW PAY
    //RULES
     */
    /*
     * The function returns 1 if there is an unresolvable conflict, a 2 if a switch can be made,
     * and a 0 if there is no conflict.
     */
    /*
     * Revised 2011-08-21 to correct an error in the $_SESSION['changeassignment']==3 section
     */
   
    $blocks = array();
    for ($x = 0; $x < 100; $x++)
    {
        $blocks[$x]=0;
    }
   
    if ($_SESSION['changeassignment']==1)
    {
        $p = "SELECT type_number, assignment
	      FROM assignments
              WHERE n={$_REQUEST['dayassignment1']}";
        $p1 = mysql_query($p);
        $p11 = @mysql_fetch_row($p1);
        $daytype1 = $p11[0];
        
        $dayassignment = trim($p11[1]);
        $p = "SELECT type_number
	      FROM assignments
              WHERE n={$_REQUEST['dayassignment2']}";
        $p2 = mysql_query($p);
        $p12 = @mysql_fetch_row($p2);
        $daytype2 = $p12[0];
/*
* If the primary assignment is a 'WEEKDAY' assignment and not 'S Bus' then check to see 
* if anyone else has that assignment.  If they do and either the begin time or the end time is
* different then send an error message.  If not, then send back the possibility of a
* switch?
* What are the $_SESSION['check'] designations for?
* $_SESSION['check'] gets set to 3 when there is the possibility of a switch.
* $_SESSION['check'] gets set to 2 when there is an overlap of assignments with another partner
* or when the begin and end time of the other partner's assignment is not standard.
*
*/
        if ($daytype1==1 && trim($p11[1])!='S Bus' && trim($p11[1]!='None' && trim($p11[1]!='SOBA')) && $daytype2!=1)
        {
            $sql001 = "SELECT mdnumber, beginblock, endblock, counter
                       FROM monthassignment
                       WHERE monthnumber={$_SESSION['dtm']}
                       AND daynumber={$_SESSION['dai']}
                       AND yearnumber={$_SESSION['dty']}
                       AND assignment LIKE '$dayassignment'";
            $sql003 = mysql_query($sql001);


            while ($currentAssignmentOtherPartner=@mysql_fetch_row($sql003))
            {
                if (!empty($currentAssignmentOtherPartner[0]) &&
                        ($currentAssignmentOtherPartner[0]!=$_SESSION['mdn']))
                {
                    $btimeperiod = $_REQUEST['btday1'];
                    $etimeperiod = $_REQUEST['etday1'];
				  
                    if
                    (
                        ($btimeperiod==$currentAssignmentOtherPartner[1])
                        && ($etimeperiod==$currentAssignmentOtherPartner[2])
                        && (!(checkdayinternals()))
                    )
                    {
                        $_SESSION['check']=3;
                        return 2;
                    }
                    /*
                     * Check to see if there is any overlap between this change and another
                     * partner's same assignment.
                     */
                    else if
                    (
                        ($btimeperiod>=$currentAssignmentOtherPartner[1]) &&
                            ($btimeperiod<$currentAssignmentOtherPartner[2])
                        ||
                        ($etimeperiod>$currentAssignmentOtherPartner[1]) &&
                            ($etimeperiod<=$currentAssignmentOtherPartner[2])
                    )
                    {
                        echo '<b><center>
                        External Error: Another partner has the same assignment with
                        different start or finishing times and overlap with your change.<br><br>';
                        $_SESSION['check']=2;
                        $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', "
                                        . "'checkdayexternals - DIFFERENT PARTNER/SAME ASSIGNMENT', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                        mysql_query($mdentryStatement);
                        return 1;
                    }
                }
            }
        }
    }


/*
 * Check to see if there is a conflict with another partner concerning Call
 */
    else if ($_SESSION['changeassignment']==3)
    {
//////////////////////////////////////////////////////////////////////////////////////////////////
//Get the normal beginblock and endblock for the selected homecallassignment chosen from the    //
//change home call page.                                                                        //
//////////////////////////////////////////////////////////////////////////////////////////////////
        $p = "SELECT assignment, beginblock, endblock
	      FROM assignments 
	      WHERE n={$_REQUEST['callassignment1']}";
        $p1 = mysql_query($p);
	$p11 = @mysql_fetch_array($p1);

	  
//////////////////////////////////////////////////////////////////////////////////////////////////
//Get the values from monthassignment which already have home call assignments of the same      //
//assignment, day, month, and year                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
	$sql001 = "SELECT mdnumber, beginblock, endblock 
	           FROM monthassignment 
		   WHERE monthnumber={$_SESSION['dtm']} 
                   AND daynumber={$_SESSION['dai']}
                   AND yearnumber={$_SESSION['dty']}
                   AND assignment='{$p11['assignment']}'
                   AND assigntype=3
                   ORDER BY beginblock";
        $sql003 = mysql_query($sql001);
	 
	  
//////////////////////////////////////////////////////////////////////////////////////////////////
//Get the blocks for selected home call assignment from the change home call assignment page.   //
//////////////////////////////////////////////////////////////////////////////////////////////////
	
        if (!empty($_REQUEST['btcall1']))
        {
            $btimeperiod1 = $_REQUEST['btcall1'];
            $etimeperiod1 = $_REQUEST['etcall1'];
        }
        if (!empty($_REQUEST['btcall2']))
        {
            $btimeperiod2 = $_REQUEST['btcall2'];
            $etimeperiod2 = $_REQUEST['etcall2'];
        }
	  
//////////////////////////////////////////////////////////////////////////////////////////////////
//Fetching the values from monthassignment.                                                     //
//Get the values not for the choosing physician.                                                //
//First, see if assignments may be switched.                                                    //
//This is done if the begin and end times match or if there is no begin time (meaning the other //
//partner doesn't have a current call assignment (may not be needed since there is a check to   //
//see if the other partner has the assignment)).                                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
        $currentAssignmentOtherPartner=@mysql_fetch_array($sql003);
	  
        if (isset($currentAssignmentOtherPartner['mdnumber']) &&
                 ($currentAssignmentOtherPartner['mdnumber']!=$_SESSION['mdn']))
        {
            if  (
                ($btimeperiod1==$currentAssignmentOtherPartner['beginblock'])
                &&
                ($etimeperiod1==($currentAssignmentOtherPartner['endblock']))
                &&
                (!(checkdayinternals()))
                ||
                (empty($btimeperiod1))
                )
                {
                    $_SESSION['check']=3;
                    return 2;
                }
			
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the conditions are not met for a switch then execution comes here.                         //
//Fill the timearray with 1's for the time segments already covered.                            //
//////////////////////////////////////////////////////////////////////////////////////////////////
                for ($c=$currentAssignmentOtherPartner['beginblock'];
                                             $c<$currentAssignmentOtherPartner['endblock']; $c++)
                {
                    $blocks[$c]=1;
                }
	
/*
 * Fetch each of the subsequent rows meeting criteria for the same assignment in monthassignment 
 * and set the time array to 1's where time segments are covered.
 */
                while ($currentAssignmentOtherPartner=@mysql_fetch_array($sql003))
                {
                    if (!empty($currentAssignmentOtherPartner['mdnumber']) &&
                            ($currentAssignmentOtherPartner['mdnumber']!=$_SESSION['mdn']))
		    {
                        for ($c=$currentAssignmentOtherPartner['beginblock'];
                                              $c<$currentAssignmentOtherPartner['endblock']; $c++)
                        {
/*
 * This happens if the other partner has a second same call assignment (if the assignment has
 * already been split.  Doesn't appear to need a time conflict check since it is already in the
 * database.
 */
                            $blocks[$c]=1;
                        }
                    }
                }
        }
	  

	  
//////////////////////////////////////////////////////////////////////////////////////////////////
//Get the segments from the change home assignment page and fill in the time array.             //
//At any point if the array already has a 1, then a conflict is present and a 1 is returned     //
//indicating a conflict.                                                                        //
//////////////////////////////////////////////////////////////////////////////////////////////////

        for ($c=$btimeperiod1; $c<$etimeperiod1; $c++)
        {
            if ($blocks[$c]==1)
            {
                echo '<b><center>External Error:  There is an overlap of time<br>
                              periods with another partner for this call assignment and<br>
                              therefore cannot be done!<br><br>';
                $_SESSION['check']=2;
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', "
                                    . "'checkdayexternals - TIME OVERLAP WITH ANOTHER PARTNER', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                return 1;
            }
            else
                $blocks[$c]=1;
        }
	
        
        
        
        if (isset($_REQUEST['callassignment2']))
        {
            $off_call2_assignment_query = mysql_query("SELECT n
                                                       FROM assignments
                                                       WHERE ah_eligible=0
                                                       AND weekend={$_SESSION['weekend']}
                                                       AND assignment LIKE 'None'");

            $off_call2_result = mysql_fetch_row($off_call2_assignment_query);
             

/*
 * Check and make sure the request is not 'None', then check to see if there is a conflict with
 * previously occupied time periods from other partners.
 */
            if (($_REQUEST['callassignment2']!= $off_call2_result[0]) && isset($btimeperiod2) )
            {
                for ($c=$btimeperiod2; $c<$etimeperiod2; $c++)
                {
                    if ($blocks[$c]==1)
                    {
                        echo '<b><center>External Error:  ! There is an overlap of time<br>
                              periods with another partner for this call assignment and<br>
                              therefore cannot be done!<br><br>';
                        $_SESSION['check']=2;
                        $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', "
                                    . "'checkdayexternals - TIME OVERLAP WITH ANOTHER PARTNER - CALL ASSIGNMENT', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                        mysql_query($mdentryStatement);
                        return 1;
                    }
                }
            }
        }
    }
    return 0;
}				  

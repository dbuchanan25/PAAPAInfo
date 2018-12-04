<?php
function checkdayinternals()
{
    /*
     * VERSION 02_02
     * FUNCTION CALLED FROM day_display.php
     * Last revised 2015-08-01
     * Revised to accomodate the way added hours have to be separated into those
     * needing an explanation or those that do not.  This required these
     * variable become $_SESSION vs. $_REQUEST variables.
     */


    /*
     * REVISED 2011-06-24 TO BETTER FORMAT THE CODE IN PREPARATION OF INSTITUTION THE NEW PAY
     * RULES
     */

    /*
     * REVISED 2011-09-24 to correct an error in allowing adding hours less than 1 hour for
     * certain assignments.
     */

    /*
     * Revised 2011-08-15 to correct the ability to add hours with a primary assignment.  Error
     * was in (!empty...  Should have been
     *  if  (
            (isset($_REQUEST['btaddedhours1']))
            &&
            ($_REQUEST['reasonaddedhours1']!=$getNoneArray[0])
        )
     */

    /*
     * Note 1
     * The timearray variable needs restructuring to accomodate the new expanded day.
     * Hopefully, this can be done without a query to the database but rather by the
     * $_REQUEST variable coming from day_display.php
     */

    /*
     * Note 2  2011-07-16
     * The $_REQUEST['btprimary'] type variables have been changed to return the timeperiod, not
     * the time.
     */

    /*
     * Note 3 2011-08-06
     * Made a change in the adding a call section where if the added call is Peds it doesn't
     * conflict with any day assignments until block 43, S C ends at 42.
     */

    $blocks = array_fill(0, 100, 0);


/*
 * ///////////////////////////////////////////////////////////////////////////////////////////////
 * Checking for any internal conflicts if the primary (OR WEEKDAY) assignment is being changed.
 */
    if ($_SESSION['changeassignment']==1)
    {
        $p = "SELECT type_number
	      FROM assignments
              WHERE n='{$_REQUEST['dayassignment1']}'";
        $p1 = mysql_query($p);
        $p11 = @mysql_fetch_row($p1);
        $daytype1 = $p11[0];

        /*
         * $primarytype could be 4, an OFF assignment.  In that case there could be no conflict
         * and is not checked.  Checking only needs to be done if the primary (OR WEEKDAY)
         * assignment is a WEEKDAY (1) assignment, not an OFF (4) assignment.
         */
	if ($daytype1==1)
	{
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the primary assignment                                                //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

/*
 * GET THE timeperiods INSTEAD OF THE time!!!!!!!!!!!!!!??????????????????????????????????????????
 */
	    $rbt = $_REQUEST['btday1'];

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the primary assignment                                                  //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $ret = $_REQUEST['etday1'];

			
            if ($ret<$rbt)
            {
                echo '<b><center>Error code: ENDBLOCK (in checkdayinternals, primary)<br><br>';
		$_SESSION['check']=1;
		return 1;
            }
			
            for ($x=$rbt; $x<$ret; $x++)
            {
                $blocks[$x]=1;
            }
        }

/*
 * Checking for conflicts with the second Day Assignment
 */
        if (!empty($_REQUEST['btday2']))
        {
            $p = "  SELECT type_number
                    FROM assignments
                    WHERE n='{$_REQUEST['dayassignment2']}'";
            $p1 = mysql_query($p);
            $p12 = @mysql_fetch_row($p1);
            $daytype2 = $p12[0];

            if ($daytype2==1)
            {
                for ($x=$_REQUEST['btday2']; $x<$_REQUEST['etday2']; $x++)
                {
                    if ($blocks[$x]==1)
                    {
                        echo '<b><center>Error code: CONFLICT WITH DAY2 ASSIGNMENT
                                (in checkdayinternals, primary)<br><br>';
                        $_SESSION['check']=1;
                        return 1;
                    }
                    else
                        $blocks[$x]=1;
                }
            }
        }
	  
	  
/*
 * Checking for conflicts with the primary (OR WEEKDAY) assignment change and any call
 * assignments.
 */
	  
        $h = "SELECT beginblock, endblock, assignment
              FROM monthassignment
              WHERE mdnumber={$_SESSION['mdn']}
	      AND monthnumber={$_SESSION['dtm']} 
              AND daynumber={$_SESSION['dai']}
              AND yearnumber={$_SESSION['dty']}
              AND assigntype=3
              ORDER BY beginblock";
        $h1 = mysql_query($h);

        /*
         * Unlike the primary (or WEEKDAY) assignment, a call assignment (WORKCALL=2 or
         * HOMECALL=3)(as defined in the "assignments" table) in the database is never
         * entered as an OFF (4) assignment, so this doesn't have to be checked.
         */
        while ($h11 = @mysql_fetch_row($h1))
        {
            for ($x=$h11[0]; $x<$h11[1]; $x++)
            {
                /*
//              Checking for Peds Call at less than the end of S_C
//              assignment (since both a Weekday assignment and the beginning of pediatric call
//              could be in the same time slot).
//              ie Technically "Peds Call" could start prior to the end of a WeedDay assignment
//              Because of this then "conflicts" before timeperiod 38 need to be ignored.
                 *
                 */

                if (trim($h11[2])=='Peds Call' && $x<43)
		{}
			
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Checking to see if there is overlap with the primary (OR WEEKDAY) assignment change and any   //
//other call assignments.                                                                       //                                                                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
                else if ($blocks[$x]==1)
                {
                    echo '<b><center>Error code: CONFLICT WITH CALL ASSIGNMENT
                            (in checkdayinternals, primary)<br><br>';
                    $_SESSION['check']=1;
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'checkdayinternals - CONFLICT WITH CALL ASSIGNMENT', CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    return 1;
                }
            }
        }


/*
 * Checking for conflicts with the primary (OR WEEKDAY) assignment change and any added hours
 * already made.
 */
        $ah = "SELECT beginblock, endblock
               FROM monthassignment
               WHERE mdnumber={$_SESSION['mdn']}
               AND monthnumber={$_SESSION['dtm']}
               AND daynumber={$_SESSION['dai']}
               AND yearnumber={$_SESSION['dty']}
               AND assigntype=4";
        $ah1 = mysql_query($ah);
        while ($ah11 = @mysql_fetch_row($ah1))
        {
            for ($x=$ah11[0]; $x<$ah11[1]; $x++)
            {
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Checking to see if there is overlap with added hours and primary (OR WEEKDAY) change.         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
                if ($blocks[$x]==1)
                {
                    echo '<b><center>Error code: CONFLICT WITH ADDED HOURS
                        (in checkdayinternals, primary)<br><br>';
                    $_SESSION['check']=1;
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}',"
                                        . "'{$_SESSION['schedmd']}', "
                                        . "'checkdayinternals - CONFLICT WITH ADDED HOURS', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    return 1;
                }
            }
        }
    }

/*
 * End of checking for internal conflicts if the primay (OR WEEKDAY) assignment has been changed.
 * ///////////////////////////////////////////////////////////////////////////////////////////////
 */


/*
 * ///////////////////////////////////////////////////////////////////////////////////////////////
 * Checking for internal conflicts when the Call Assignment has been changed
 */
    else if ($_SESSION['changeassignment']==3)
    {
	  
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the homecall assignment                                               //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        /*
         * In the new assignment list 78 and 80 are 'None'
         * If someone is changing the call assignment to None, then no internal conflict checking
         * needs to be done.
         * Only in the case of someone changing the call assignment to something besides None does
         * the internal conflict checking need to be done.
         */
        if ($_REQUEST['callassignment1']!=78 && $_REQUEST['callassignment1']!=80)
        {

            $getCallTypeStatement = "SELECT type_number
                                     FROM assignments
                                     WHERE n={$_REQUEST['callassignment1']}";
            $getCallTypeQuery = mysql_query($getCallTypeStatement);
            $getCallTypeArray = @mysql_fetch_row($getCallTypeQuery);
            $callType1 = $getCallTypeArray[0];
            

            $rbt = $_REQUEST['btcall1'];


/*
 * Why is the next line necessary?  What is its purpose?
 * It allows for when a Peds Call is switched then it backs it up to timeperiod 34 to make sure
 * the partner gets all the credit they deserve.
 * This may allow an error if a partner wants to take Peds call from timeperiod 34 up to 42 before
 * another partner takes over the Peds Call assignment but this would be unlikely.
 * THIS MAY NEED TO BE CHANGED TO MORE EXACTLY ALLOW FOR THIS POSSIBILITY?????????????????????????
 */
/*
 * Assignment 51 is Peds Call WD in the new Assignment Table
 */
            if ($_REQUEST['callassignment1']==51 && ($rbt>34 && $rbt<43))
                $rbt=34;


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the homecall assignment                                                 //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
/*
 * GET THE timeperiods INSTEAD OF THE time!!!!!!!!!!!!!!??????????????????????????????????????????
 */

            $ret = $_REQUEST['etcall1'];


/*
 * This was in the original code.
 * Why would this be necessary????????????????????????????????????????????????????????
if ($_REQUEST['callassignment']==51 && ($ret>34 && $ret<40))
    $ret=40;
 *
 */
			
            if ($ret<$rbt)
            {
                echo '<b><center>Error code: ENDBLOCK (in checkdayinternals, homecall 1)<br><br>';
                $_SESSION['check']=1;
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}',"
                                    . "'{$_SESSION['schedmd']}', "
                                    . "'checkdayinternals - ENDBLOCK (in checkdayinternals, homecall 1)', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                return 1;
            }
				
            for ($x=$rbt; $x<$ret; $x++)
            {
                if ($callType1==2)
                    $blocks[$x]=2;
                else
                    $blocks[$x]=1;
            }
        }
		 
		 
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the homecall assignment 2                                             //
// $rbt2                                                                                        //
// Assignments 69 and 53 are 'NONE'                                                             //
// Assignments 78 and 80 are 'None' in the new assignment list (2011-06)                        //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        if ($_REQUEST['callassignment2']!=78 && $_REQUEST['callassignment2']!=80)
        {
            $getCallTypeStatement = "SELECT type_number, beginblock, endblock
                                     FROM assignments
                                     WHERE n={$_REQUEST['callassignment2']}";
            $getCallTypeQuery = mysql_query($getCallTypeStatement);
            $getCallTypeArray = @mysql_fetch_row($getCallTypeQuery);
            $callType2 = $getCallTypeArray[0];


            $rbt2 = $_REQUEST['btcall2'];

            /*
             * Check for Peds call
             */

            if ($_REQUEST['callassignment2']==51 && $rbt2>34 && $rbt2<43)
                $rbt2=34;

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the homecall 2 assignment                                               //
// $ret2                                                                                        //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            $ret2 = $_REQUEST['etcall2'];

            /*
            if ($_REQUEST['callassignment2']==51 && ($ret2>35 && $ret2<40))
                
                 //In the new system 40 may not be correct
                 
                $ret2=40; 
             */

/*
 * Make sure the selected times for callassignment2 fall within the normal time bounds of that
 * assignment for a specific day.  Don't want a selected assignment to run before or after the
 * normal blocks (for example: blocks 1 or 2 or 99 or 100) for a particular day.
 *
 * This could be the case because the javascript doesn't send back the normal time blocks for
 * call2, so all of them must be displayed.
 */
            if ($getCallTypeArray[1]>$rbt2 || $getCallTypeArray[2]<$ret2)
            {
                echo '<b><center>Error code: OUTSIDE ASSIGNMENT TIME BOUNDS</center><br>
                      <center>(in checkdayinternals, homecall 2)</center><br><br>';
                $_SESSION['check']=1;
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}',"
                                    . "'{$_SESSION['schedmd']}', "
                                    . "'checkdayinternals - OUTSIDE ASSIGNMENT TIME BOUNDS', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                return 1;
            }

            if ($ret2<$rbt2)
            {
                echo '<b><center>Error code: ENDBLOCK (in checkdayinternals, homecall 2)<br><br>';
                $_SESSION['check']=1;
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}', "
                                    . "'{$_SESSION['schedmd']}', "
                                    . "'checkdayinternals - ENDBLOCK (in checkdayinternals, homecall 2)', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                return 1;
            }
				
            for ($x=$rbt2; $x<$ret2; $x++)
            {
                if ($blocks[$x]==1 || $blocks[$x]==2)
                {
                    echo '<b><center>Error Code: CALL 1 & 2 CONFLICT
                        (in checkdayinternals, homecall 2)<br><br>';
                    $_SESSION['check']=1;
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}', "
                                        . "'{$_SESSION['schedmd']}', "
                                        . "'checkdayinternals - CALL 1 & 2 CONFLICT - homecall 2)', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    return 1;
                }
                else
                {
                    if ($callType2==2)
                        $blocks[$x]=2;
                    else
                        $blocks[$x]=1;
                }
            }
        }
  

/*
 * Check to see if the CALL assignment change conflicts with the primary (WEEKDAY) assignment.
 */
         $p = "SELECT beginblock, endblock, assignment, weekend 
	       FROM monthassignment  
	       WHERE mdnumber={$_SESSION['mdn']} 
	       AND monthnumber={$_SESSION['dtm']}
               AND daynumber={$_SESSION['dai']}
               AND yearnumber={$_SESSION['dty']}
               AND assigntype=1";
	 $p1 = mysql_query($p);

/*
 * There may be more than one primary (WEEKDAY) assignment and each needs to be checked.
 */
        while ($p11 = @mysql_fetch_array($p1))
        {
            $pt = "SELECT type_number
                   FROM assignments
                   WHERE assignment='$p11[2]'
                   AND weekend=$p11[3]
                   AND type_number!=4";
            $pt1 = mysql_query($pt);
            $pt11 = @mysql_fetch_row($pt1);
            $primarytype = $pt11[0];
            if ($primarytype==1)
            {
                for ($x=$p11[0]; $x<$p11[1]; $x++)
                {
                    if ($blocks[$x]==1 || $blocks[$x]==2)
                    {
/*
 * If the call assignment being added is Peds Call then no conflict should occur before block 43.
 * Week-day Peds call is 51
 * Block 42 is when S C ends.
 */
                        if (
                            $p11['beginblock']<43
                            &&
                            ($_REQUEST['callassignment1']==51 || $_REQUEST['callassignment2']==51)
                           )
                        {}

                        else
                        {
                            echo '<b><center>Error: CONFLICT PRIMARY ASSIGNMENT
                                (in checkdayinternals, homecall)<br><br>';
                            $_SESSION['check']=1;
                            $mdentryStatement = "INSERT INTO mdlog ".
                                                "VALUES ('{$_SESSION['initials']}', "
                                                . "'{$_SESSION['schedmd']}', "
                                                . "'checkdayinternals - CONFLICT PRIMARY ASSIGNMENT - homecall)', "
                                                . "CURRENT_TIMESTAMP,NULL)";
                            mysql_query($mdentryStatement);
                            return 1;
                        }
                    }
                }
            }
        }
        /*
        else
        {
            echo '<b><center>Error code: 425 in checkdayinternals<br><br>';
            $_SESSION['check']=1;
            return 1;
        }
         *
         */

        /*
        $s = "SELECT beginblock, endblock, assignment, weekend
              FROM monthassignment
	      WHERE mdnumber={$_SESSION['mdn']}  
	      AND monthnumber={$_SESSION['dtm']} 
              AND daynumber={$_SESSION['dai']}
              AND yearnumber={$_SESSION['dty']}
              AND assigntype=2";
        $s1 = mysql_query($s);
        $s11 = @mysql_fetch_row($s1);
        if (!empty($s11))
        {
            $st = "SELECT type
		   FROM assignments
                   WHERE assignment='$s11[2]'
                   AND weekend=$s11[3]
		   AND home=0";
            $st1 = mysql_query($st);
            $st11 = @mysql_fetch_row($st1);
            $secondarytype = $st11[0];
            if (trim($secondarytype)=='WORK')
            {
                for ($x=$s11[0]; $x<$s11[1]; $x++)
                {
                    if ($blocks[$x]==1)
                    {
                        echo '<b><center>Error code: WORKCALL CONFLICT
                            (checkdayinternals, homecall)<br><br>';
                        $_SESSION['check']=1;
                        return 1;
                    }
                }
            }
        }
         * 
         */



 /*
  * Check to see if the CALL assignment (only a WORKCALL) change conflicts with any added hours.
  * In $blocks[] the WORKCALL assignment blocks have been assigned a value of 2.  The only concern
  * is with the WORKCALL assignments, not the HOMECALL assignments in which $blocks[] have been
  * assigned the value 1.
 */
        $p = "SELECT beginblock, endblock
	      FROM monthassignment
	      WHERE mdnumber={$_SESSION['mdn']}
	      AND monthnumber={$_SESSION['dtm']}
	      AND daynumber={$_SESSION['dai']}
              AND yearnumber={$_SESSION['dty']}
              AND assigntype=4";
	$p1 = mysql_query($p);

        while ($p11 = @mysql_fetch_row($p1))
        {
            for ($x=$p11[0]; $x<$p11[1]; $x++)
            {
                if ($blocks[$x]==2)
                {
                    echo '<b><center>Error: CONFLICT BETWEEN CALL CHANGE AND ADDED HOURS
                        (in checkdayinternals, homecall)<br><br>';
                    $_SESSION['check']=1;
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}', "
                                        . "'{$_SESSION['schedmd']}', "
                                        . "'checkdayinternals - CONFLICT BETWEEN CALL CHANGE AND ADDED HOURS - homecall', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    return 1;
                }
            }
        }
    }
/*
 * End of checking for internal conflicts if a CALL assignment has been changed.
 */


    
    
    
    
    
    
    
    
    
    
    
    
    
/*
 * ///////////////////////////////////////////////////////////////////////////////////////////////
 * Checking for internal conflicts if added hours has been changed.
 */
    else if ($_SESSION['changeassignment']==4)
    {
        $totaladdedhours=0;
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the added hours 1 assignment                                          //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

/*
 * Get the appropriate assignment n for 'None' - whether it is WD or WE
 */
    $getNoneQuery = "SELECT n
                     FROM assignments
                     WHERE assignment LIKE 'None%'
                     AND weekend={$_SESSION['weekend']}
                     AND callassignment=0
                     AND addhours=1";
    $getNoneResult = mysql_query($getNoneQuery);
    $getNoneArray = @mysql_fetch_row($getNoneResult);


    /*
     * If someone is adding hours outside the normal boundaries for their assignment, an internal
     * conflict must be thrown.
     */
    
    /*
     * Accomodating the Adding Hours After Call at PAAPA Request added hours
     * Making sure this is done in the appropriate situation.
     */
    if  (isset($_SESSION['reasonaddedhours1']) && $_SESSION['reasonaddedhours1'] == 92) //92 is Adding Hours After Call at PAAPA Request
    {
        if ($_SESSION['btaddedhours1']<2
            ||
            $_SESSION['etaddedhours1']<3)
        {
             echo '<b><center>Error code: ADDING HOURS AFTER CALL CONFLICT
                    (in checkdayinternals, added hours 1)<br><br>';
             $_SESSION['check']=1;
             
             $_SESSION['reasonaddedhours1']!=$getNoneArray[0];
             
             $mdentryStatement = "INSERT INTO mdlog ".
                                 "VALUES ('{$_SESSION['initials']}', "
                                 . "'{$_SESSION['schedmd']}', "
                                 . "'checkdayinternals - ADDING HOURS AFTER CALL CONFLICT - added hours 1)', "
                                 . "CURRENT_TIMESTAMP,NULL)";
             mysql_query($mdentryStatement);
             
             return 1;
        }
    }

   
    /*
     * If the added hours are NOT 'None' then the hours are checked for 
     * appropriate timing.
     */
    if  (
            (isset($_SESSION['btaddedhours1']))
            &&
            ($_SESSION['reasonaddedhours1']!=$getNoneArray[0])
        )
        {
            $rbt = $_SESSION['btaddedhours1'];


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the added hours 1 assignment                                            //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            $ret = $_SESSION['etaddedhours1'];
			

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// If endblock is before beginblock return 1                                                    //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

            if ($ret<$rbt)
            {
                echo '<b><center>Error code: END BLOCK CONFLICT
                    (in checkdayinternals, added hours 1)<br><br>';
                $_SESSION['check']=1;
                
                $_SESSION['reasonaddedhours1']!=$getNoneArray[0];
                
                return 1;
            }
			

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Setting the blocks between beginblock and endblock to 1                                      //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

            for ($x=$rbt; $x<$ret; $x++)
            {
                if ($blocks[$x]==1){
                    echo '<b><center>Error code: ADDED HOURS 1 CONFLICT
                        (in checkdayinternals, addedhours 2)<br><br>';
                    $_SESSION['check']=1;
                    
                    $_SESSION['reasonaddedhours1']!=$getNoneArray[0];
                    
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}', "
                                        . "'{$_SESSION['schedmd']}', "
                                        . "'checkdayinternals - ADDED HOURS 1 CONFLICT - addedhours 2', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    
                    return 1;
                }
                else {
                $blocks[$x]=1;
                $totaladdedhours++;
                }
            }
        }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the added hours 2 assignment                                          //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

        if  (
                (isset($_SESSION['btaddedhours2']))
                &&
                ($_SESSION['reasonaddedhours2']!=$getNoneArray[0])
            )
        {
            $rbt = $_SESSION['btaddedhours2'];


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the added hours 2 assignment                                            //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $ret = $_SESSION['etaddedhours2'];

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// If endblock is before beginblock return 1                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            if ($ret<$rbt)
            {
                echo '<b><center>Error code: ENDBLOCK CONFLICT
                        (in checkdayinternals, addedhours 2)<br><br>';
                $_SESSION['check']=1;
                
                $_SESSION['reasonaddedhours2']!=$getNoneArray[0];
                
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}', "
                                    . "'{$_SESSION['schedmd']}', "
                                    . "'checkdayinternals - ENDBLOCK CONFLICT - addedhours 2', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                
                return 1;
            }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Setting the blocks between beginblock and endblock to 1                                      //
// If a block already == 1 from already being set, then return 1                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            for ($x=$rbt; $x<$ret; $x++)
            {
                if ($blocks[$x]==1)
                {
                    echo '<b><center>Error code: ADDED HOURS 1 CONFLICT
                        (in checkdayinternals, addedhours 2)<br><br>';
                    $_SESSION['check']=1;
                    
                   $_SESSION['reasonaddedhours2']!=$getNoneArray[0];
                    
                    return 1;
                }
                else
                {
                    $blocks[$x]=1;
                    $totaladdedhours++;
                }
            }
        }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for the added hours 3 assignment                                          //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        /*
         * This needs to be changed. ??????????????????????????
         */
        if  (
                (isset($_SESSION['btaddedhours3']))
                &&
                ($_SESSION['reasonaddedhours3']!=$getNoneArray[0])
            )
		   
        {
            $rbt = $_SESSION['btaddedhours3'];


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the added hours 3 assignment                                            //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $ret = $_SESSION['etaddedhours3'];


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// If endblock is before beginblock return 1                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            if ($ret<$rbt)
            {
                echo '<b><center>Error code: ENDBLOCK CONFLICT (in checkdayinternals, addedhours 3)
                    <br><br>';
                $_SESSION['check']=1;
                
                $_SESSION['reasonaddedhours3']!=$getNoneArray[0];
                
                $mdentryStatement = "INSERT INTO mdlog ".
                                    "VALUES ('{$_SESSION['initials']}', "
                                    . "'{$_SESSION['schedmd']}', "
                                    . "'checkdayinternals - ENDBLOCK CONFLICT - addedhours 3', "
                                    . "CURRENT_TIMESTAMP,NULL)";
                mysql_query($mdentryStatement);
                
                return 1;
            }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Setting the blocks between beginblock and endblock to 1                                      //
// If a block already == 1 from already being set, then return 1                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            for ($x=$rbt; $x<$ret; $x++)
            {
                if ($blocks[$x]==1)
                {
                    echo '<b><center>Error code: ADDED HOURS CONFLICT
                        (in checkdayinternals, addedhours 3)<br><br>';
                    $_SESSION['check']=1;
                    
                    $_SESSION['reasonaddedhours3']!=$getNoneArray[0];
                    
                    $mdentryStatement = "INSERT INTO mdlog ".
                                        "VALUES ('{$_SESSION['initials']}', "
                                        . "'{$_SESSION['schedmd']}', "
                                        . "'checkdayinternals - ADDED HOURS CONFLICT - addedhours 3', "
                                        . "CURRENT_TIMESTAMP,NULL)";
                    mysql_query($mdentryStatement);
                    
                    return 1;
                }
                else
                {
                    $blocks[$x]=1;
                    $totaladdedhours++;
                }
            }
        }
	  
        $p = "SELECT beginblock, endblock, assignment, weekend
              FROM monthassignment
              WHERE mdnumber={$_SESSION['mdn']}
              AND monthnumber={$_SESSION['dtm']}
              AND daynumber={$_SESSION['dai']}
              AND yearnumber={$_SESSION['dty']}
              AND assigntype=1";
        $p1 = mysql_query($p);

        while ($p11=@mysql_fetch_row($p1))
        {
            $pt = "SELECT type_number
                   FROM assignments
                   WHERE assignment='$p11[2]'
                   AND weekend=$p11[3]
                   AND ah_eligible=0";
            $pt1 = mysql_query($pt);
            $pt11 = @mysql_fetch_row($pt1);
            $primarytype = $pt11[0];



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// If it is a WORK primary assignment, then set those blocks to 1                               //
// If a block already == 1 from already being set, then return 1                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
            if ($primarytype==1)
            {
                for ($x=$p11[0]; $x<$p11[1]; $x++)
                {
                    if ($blocks[$x]==1)
                    {
                        echo '<b><center>Error code: DAY ASSIGNMENT CONFLICT
                            (in checkdayinternals, addedhours)<br><br.';
                        $_SESSION['check']=1;
                        
                        $mdentryStatement = "INSERT INTO mdlog ".
                                            "VALUES ('{$_SESSION['initials']}', "
                                            . "'{$_SESSION['schedmd']}', "
                                            . "'checkdayinternals - DAY ASSIGNMENT CONFLICT - addedhours', "
                                            . "CURRENT_TIMESTAMP,NULL)";
                        mysql_query($mdentryStatement);
                        return 1;
                    }
                    else
                    {
                        $blocks[$x] = 1;
                    }
                }
            }
        }

        /*
         * Check to see if adding hours conflicts with an in-house call (type_number=2) in the
         * assignments table of the database
         */

        $p = "SELECT beginblock, endblock, assignment, weekend
              FROM monthassignment
              WHERE mdnumber={$_SESSION['mdn']}
              AND monthnumber={$_SESSION['dtm']}
              AND daynumber={$_SESSION['dai']}
              AND yearnumber={$_SESSION['dty']}
              AND assigntype=3";
        $p1 = mysql_query($p);

        while ($p11=@mysql_fetch_row($p1))
        {
            $pt = "SELECT type_number
                   FROM assignments
                   WHERE assignment='$p11[2]'
                   AND weekend=$p11[3]
                   AND (
                        type_number=2
                        OR
                        type_number=6
                       )
                  ";
            $pt1 = mysql_query($pt);
            $pt11 = @mysql_fetch_row($pt1);
            $callType = $pt11[0];



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// If it is a WORK primary assignment, then set those blocks to 1                               //
// If a block already == 1 from already being set, then return 1                                //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            if ($callType==2)
            {
                for ($x=$p11[0]; $x<$p11[1]; $x++)
                {
                    if ($blocks[$x]==1)
                    {
                        echo '<b><center>Error code: IN HOUSE CALL CONFLICT
                            (in checkdayinternals, addedhours)<br><br.';
                        $_SESSION['check']=1;
                        $mdentryStatement = "INSERT INTO mdlog ".
                                            "VALUES ('{$_SESSION['initials']}', "
                                            . "'{$_SESSION['schedmd']}', "
                                            . "'checkdayinternals - IN HOUSE CALL CONFLICT - addedhours', "
                                            . "CURRENT_TIMESTAMP,NULL)";
                        mysql_query($mdentryStatement);
                        return 1;
                    }
                    else
                    {
                        $blocks[$x] = 1;
                    }
                }
            }
        }

        /*
         * In some cases added hours do not need to be at least one hour.
         * These cases are the short day assignments and C_OH.
         * They are described in the variable $earlyAdd, essentially a boolean denoting whether
         * an assignment is eligible for added hours less that 4 time periods without a conflict.
         */

        $presentMDAssignmentsQuery = "SELECT assignment, weekend, assigntype
                                      FROM monthassignment
                                      WHERE mdnumber={$_SESSION['mdn']}
                                      AND monthnumber={$_SESSION['dtm']}
                                      AND daynumber={$_SESSION['dai']}
                                      AND yearnumber={$_SESSION['dty']}";
        $presentMDAssignmentResult = mysql_query($presentMDAssignmentsQuery);

        $earlyAdd = 0;

        while ($presentMDAssignmentArray = mysql_fetch_row($presentMDAssignmentResult))
        {
           if ((trim($presentMDAssignmentArray[0])=='COPS2')
             ||(trim($presentMDAssignmentArray[0])=='Smat2')
             ||(trim($presentMDAssignmentArray[0])=='Shnt2')
             ||(trim($presentMDAssignmentArray[0])=='C OH' && $presentMDAssignmentArray[1]==0
                && $presentMDAssignmentArray[2]==3
                )
               )
           {
               $earlyAdd = 1;
           }
        }


        if ($totaladdedhours<4 && $totaladdedhours!=0 && $earlyAdd==0)
        {
            echo '<b><center>Error code: TOO LITTLE TIME ADDED (in checkdayinternals, totaltime)
                <br><br>';
            $mdentryStatement = "INSERT INTO mdlog ".
                                "VALUES ('{$_SESSION['initials']}', "
                                . "'{$_SESSION['schedmd']}', "
                                . "'checkdayinternals - TOO LITTLE TIME ADDED', "
                                . "CURRENT_TIMESTAMP,NULL)";
            mysql_query($mdentryStatement);
            return 1;
        }
    }
    return 0;
}



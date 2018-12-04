<?php 
if (!isset($_SESSION)) { session_start(); } 

/*
 * VERSION 02_04
 * Revised: 2015-08-02
 * Revised: 2014-07-30
 * Revised: 2014-01-13
 * Revised 2014-01-08
 * Revised 2011-07-20
 * Revised 2011-06-26
 * Revised 2011-06-24
 * Revised 2011-04-02
 * Revised 2010-10-20
 * 
 * Previous page:  "choose2.php"
 * Next page:  Generally, this page.  However, any of the menu pages can be
 *             accessed from this page.
 */


/*
//REVISED 2010-10-20
//REVISION ON 2010-10-20 PUT IN ALERTS FOR CHOOSING REASONS FOR ADDING HOURS NOT RELATED TO
//A USER'S HOME CALL.
 */

/*
//REVISED 2011-04-02
//FORMATTED THE CODE TO BE ABLE TO PRINT IT 
//FORMATTED THE MYSQL QUERIES BETTER
 */

/*
 * REVISED 2011-06-24,25
 * REFORMATTED THE SWITCH PART OF THE CODE (THE FIRST THIRD) AND INSPECTED IT FOR ERRORS.
 * IN THE NEW PAY SCHEME A COUPLE OF QUESTIONS.
 * 1.  DO THE WARNINGS ABOUT SWITCHING C OH AND H 1 NEED TO BE REVISED???????????????????????????
 *      LINES: 91, 101, 145, 156, AND IN SWITCHING CALL ASSIGNMENT ???????????????????????????????
 * 2.  DOES ITEM 5 (ASSIGNMENT TYPE) NEED TO BE SWITCHED WHEN SWITCHING THE ASSIGNMENTS??????????
 *      LINES:  230, 310, 500, 570 ??????????????????????????????????????????????????????????????
 */

/*
 * REVISED 2011-06-26
 * REFORMATTED THE REST OF THE CODE NOT REFORMATTED 2011-06-24,25
 * 1.  SECONDARY ASSIGNMENT NEEDS SCRUTINY - DISCONTINUED
 * 2.  PRIMARY ASSIGNMENT NEEDS TO BE ABLE TO TAKE TWO ENTRIES IF NECESSARY
 * 3.  NEED SOME WAY TO SPLIT AN ASSIGNMENT BETWEEN TWO PARTNERS
 * 4.  ****IMPORTANT****  THE TIMEPERIODS NEED TO BE TRANSMITTED, NOT THE TIMES. - DONE
 */

/*
 * Revised 2011-07-20
 * Transferred the majority of the day_display display code to day_display_code.php.
 * This affects SECTION 3 & 4.
 * daydisplay() & timedisplay() were renamed day_display_code1 and day_display_code2
 * to better delineate flow of code.
 *
 * Flow -> day_display.php -> function day_display_code.php -> day_display_code1.php ->
 * day_display_code2.php
 */

/*
 * Revised 2014-01-08
 * Updated to be able to correctly update monthcal in its newer format showing
 * start and stop times instead of hour deviations.
 */

/*
 * Revised 2014-07-30
 * Placed code to log access and insert statements
 */

/*
 * Revised 2015-08-02
 * Improved log code and INSERT statements for MySQL so they will log better.
 */

/*
 * $_SESSION['schedmd']    = initials for the "schedule for" physician
 * $_SESSION['initials']   = initials for the "using" physician
 * $_SESSION['schedmdnum'] = md number for the "schedule for" physician
 * $_SESSION['dty']        = year
 * $_SESSION['dtm']        = month (numerical)
 * $_SESSION['mn']         = month (alphabetical)
 * $_SESSION['schedchange']= 1 if the schedule has been changed to notify this
 *                           section of the code where to deal with the choices
 * $_SESSION['changeassignment'] indicates which part of the schedule is being
 *                               changed, 1 if day, 2 if call
 * $_SESSION['sqlassfetch'] is the assignment which the user is picking to 
 *                          switch to.
 * $_SESSION['check'] gets set to 3 when denoting that a switch is possible
 * $_SESSION['check'] gets set to 2 when there is an overlap of assignments with 
 *                    another partner or when the begin and end time of the 
 *                    other partner's assignment is not standard (an external
 *                    conflict)
 * $_SESSION['check'] gets set to 1 if an internal conflict is present
 */

if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}


else
{

    /*
     * All the includes necessary
     */
       include ('includes/header.php');
       require_once ($_SESSION['login2string']);

       include ('dayassign.php');
       include ('callassign.php');
       include ('addhours.php');
       include ('splitAssignment.php');

       include ('checkdayinternals.php');
       include ('checkdayexternals.php');

       include ('isweekend.php');

       include ('day_display_code.php');  //previously in SECTIONS 3 & 4 of this code
       include ('day_display_code1.php'); //previously daydisplay.php
       include ('day_display_code2.php'); //previously timedisplay.php

       include ('switch_PrimaryAssignment.php');
       include ('switch_HomeCall.php');
       
       $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
       


   
/*
 * SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1
 * SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1
 * SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1 SECTION 1
 */

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// IF THIS PAGE HAS ALREADY BEEN ACCESSED AND SOMETHING CHANGED,                                //
// THEN THIS CODE WILL TAKE CARE OF IT                                                          //
// $_SESSION['schedchange'] is checked to see if it has been set to 1.                          //
// $_SESSION['changeassignment'] is checked to see what its value is: (1=Day,                   //
// 2=Call) (Which assignment is being changed.)                                                 //
// $_REQUEST['switch'] is set to 1 if an external conflict has taken place but conditions are   // 
// correct for a switch with another partner                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

   /*
    * $_SESSION['sqlassfetch'] is the assignment which the user is picking to switch to.
    * 
    * $_REQUEST['switch'] gets set to 1 if a switch is to made, 0 if not.
    * This happens at about line 887 in SECTION 2 after checkdayexternals() sets
    * $_SESSION['check']==3 denoting that a switch is possible.
    * SEE SECTION 2.
    */

    if (isset($_REQUEST['switch'])&&($_REQUEST['switch']==1))
      /*
       * At this point the switch has been determined to be doable.
       */
    {
        if ($_SESSION['changeassignment']==1)
         /*
          * SWITCH FOR A PRIMARY ASSIGNMENT
          * If the assignment to be changed is the primary assignment,
          * then $_SESSION['changeassignment']==1
          */
        {
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - direct to switch_PrimaryAssignment', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);

           switch_PrimaryAssignment();
        }

/*
* ///////////////////////////////////////////////////////////////////////////////////////
* SWITCH FOR A CALL ASSIGNMENT
* Essentially, the same process is done for a call assignment as for a primary
* assignment.
* $_SESSION['changeassignment']==3 indicates a switch in a call assignment.
* ///////////////////////////////////////////////////////////////////////////////////////
*/	 
        else if ($_SESSION['changeassignment']==3)
        {
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - direct to switch_HomeCall', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);


            switch_HomeCall();
        }  
	 
     echo '<center>  
           <FORM METHOD="LINK" ACTION="day_display.php">
           <INPUT TYPE="submit" VALUE="Submit">
           </FORM>
	   <br>
           <br>
          ';
		   
     include ('includes/footer.html');
  }

 
 
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
/*
* SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2
* SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2
* SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2 SECTION 2
*/
/*
* If the program comes here and either checkdayinternals or checkdayexternals returns
* true then there is a conflict.
* The program then displays this and gives the user the opportunity to go back to a previous
* starting point.
*
* For conflicts, the $_SESSION['check'] variable is set.
* What are the $_SESSION['check'] designations for?
* $_SESSION['check'] gets set to 3 when there is the possibility of a switch.
* $_SESSION['check'] gets set to 2 when there is an overlap of assignments with another partner
* or when the begin and end time of the other partner's assignment is not standard.
*
*/
    else if (($_SESSION['schedchange']==1) &&
             (checkdayinternals()||checkdayexternals()))
    {
        if ($_SESSION['check']==1)
        {
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - INTERNAL CONFLICT 1', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);

            echo '
                <h1 align="center">Conflict Page</h1>
                <br>
                <p align="center">
                <h3 align="center">
                There is an INTERNAL CONFLICT!
		<br>
		</p>
		<p align="center">
		A conflict has been detected in the change you have tried to make.
	        <br>There are two major reasons for this.
	        <br>
	        <p>
                <br>
	        </p>
	        <p align="center">
	        <br>1.  You may have tried to make a change which conflicts with your own
                schedule for that day,
	        <br><tab>    such as trying to schedule one assignment at the same time of
                another.
	        </p>
	        <br>
	        <p align="center">
	        <br>2. You may have tried to make a change which conflicts with another partner\'s
                assignment.
	        <br><tab>    If you are sharing an assignment with another partner, you must
                shorten their
	        <br><tab>    assignment first before adding the portion of the assignment you are
                working.
	        </p>
	        <br>
	        <p>
	        </p>
	        <p align="center">Please reevaluate your change.
		<br>If you do not know what the problem is, contact Dale Buchanan.
	        <br>To go back to the day assignment page, press the Submit button below.
	        </h3>
	        <br>
	        <br>
	        ';

            $_SESSION['schedchange']=2;

            echo '
                <center>
                <FORM METHOD="LINK" ACTION="day_display.php">
                <INPUT TYPE="submit" VALUE="Submit">
                </FORM>
                <br>
                 ';
            include ('includes/footer.html');
        }
        /*
         * When $_SESSION['check'] == 2, no switch may be made and the conflict 
         * must be displayed and the change disallowed.
         */
        else if ($_SESSION['check']==2)
        {
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - EXTERNAL CONFLICT 1', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);
            
            echo '
                <h1 align="center">Conflict Page</h1>
                <br>
                <p align="center">
                <h3 align="center">
                There is an EXTERNAL CONFLICT!
		<br>
		</p>
		<p align="center">
		A conflict has been detected in the change you have tried to make.
	        <br>There are two major reasons for this.
	        <br>
	        <p>
                <br>
	        </p>
	        <p align="center">
	        <br>1.  You may have tried to make a change which conflicts with your own
                schedule for that day,
	        <br><tab>    such as trying to schedule one assignment at the same time of
                another.
	        </p>
	        <br>
	        <p align="center">
	        <br>2.  You may have tried to make a change which conflicts with another partners
                assignment.
	        <br><tab>    If you are sharing an assignment with another partner, you must
                shorten their
	        <br><tab>    assignment first before adding the portion of the assignment you are
                working.
	        </p>
	        <br>
	        <p>
	        </p>
	        <p align="center">Please reevaluate your change.  
		<br>If you do not know what the problem is, contact Dale Buchanan.
	        <br>To go back to the day assignment page, press the Submit button below.
	        </h3>
	        <br>
	        <br>
	        ';

            $_SESSION['schedchange']=2;

            echo '
                <center>
                <FORM METHOD="LINK" ACTION="day_display.php">
                <INPUT TYPE="submit" VALUE="Submit">
                </FORM>
                <br>
                 ';
            include ('includes/footer.html');
        }

        /*
         * If $_SESSION['check'] == 3, then a switch is allowable and the appropriate page
         * displayed for this.
         */
        else if ($_SESSION['check']==3)
        {
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - EXTERNAL CONFLICT 2', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);

            echo "
                <h1 align='center'>Conflict Page</h1>
                <br>
                <p align='center'>
                <h3 align='center'>
                There is an EXTERNAL CONFLICT!
                <br>
                </p>
                <p align='center'>
                A conflict has been detected in the change you have tried to make.
                </p>
                <p align='center'>
                <br>
	        <br>Another partner has the same assignment for the same time you are trying to
                schedule.
		<br>If you want to switch assignments with this partner, select YES below.
		<br>If not, select NO.
		</p>
	        </h3>
		<br><br>
		  ";
				
            if ($_SESSION['changeassignment']==1)
            {
                $sqlass = "SELECT assignment
		           FROM assignments 
                           WHERE n={$_REQUEST['dayassignment1']}";
                $sqlassquery = mysql_query($sqlass);
                $sqlassfetch = @mysql_fetch_row($sqlassquery);
                $_SESSION['sqlassfetch']=$sqlassfetch[0];
				   
                $sqlsame = "SELECT mdnumber
                            FROM monthassignment
                            WHERE  monthnumber={$_SESSION['dtm']}
		            AND daynumber={$_SESSION['dai']}
                            AND yearnumber={$_SESSION['dty']}
                            AND assignment='$sqlassfetch[0]'";
		$sqlsamequery = mysql_query($sqlsame);
		$sqlsamefetch = @mysql_fetch_row($sqlsamequery);
			   
		$sqlname = "SELECT * 
		            FROM mds 
                            WHERE number=$sqlsamefetch[0]";
		$sqlnamequery = mysql_query($sqlname);
		$sqlnamefetch = @mysql_fetch_row($sqlnamequery);
				   
		 echo '
                    <h3>
                    <p align="center">
                    Dr. '.$sqlnamefetch[1].' '.$sqlnamefetch[0].' is already assigned '.
                    $sqlassfetch[0].' for '.$_SESSION["dtm"].'/'.$_SESSION["dai"].'/'.
                    $_SESSION["dty"].'.
                    </p>
                    </h3>
                       ';
            }
            else if ($_SESSION['changeassignment']==3)
            {
		//$_REQUEST['callassignment1'] is the number of the assignment
                $sqlass = "SELECT assignment
		           FROM assignments 
                           WHERE n={$_REQUEST['callassignment1']}";
		$sqlassquery = mysql_query($sqlass);
		$sqlassfetch = @mysql_fetch_row($sqlassquery);
		$_SESSION['sqlassfetch']=$sqlassfetch[0];
		//$_SESSION['sqlassfetch'] is the session variable with the assignment name
				   
		$sqlsame = "SELECT mdnumber 
		            FROM monthassignment 
                            WHERE  monthnumber={$_SESSION['dtm']}
                            AND daynumber={$_SESSION['dai']}
                            AND yearnumber={$_SESSION['dty']}
                            AND assignment='$sqlassfetch[0]'
                            AND assigntype=3";
		$sqlsamequery = mysql_query($sqlsame);
		$sqlsamefetch = @mysql_fetch_row($sqlsamequery);
				   
		$sqlname = "SELECT * FROM mds WHERE number=$sqlsamefetch[0]";
		$sqlnamequery = mysql_query($sqlname);
		$sqlnamefetch = @mysql_fetch_row($sqlnamequery);
				   
		echo '
                    <h3>
                    <p align="center">
                    Dr. '.$sqlnamefetch[1].' '.$sqlnamefetch[0].' is already assigned '.
                    $sqlassfetch[0].' for '.$_SESSION["dtm"].'/'.$_SESSION["dai"].'/'.
                    $_SESSION["dty"].'.
                    </p>
                    </h3>
                     ';
            }
				          	
            echo "
                <br>
                <br>
		<center>
		<form method='post' action='day_display.php'>
		<select name='switch'>
  		<option value=1>YES</option>
  		<option value=0>NO</option>
		</select>
		  ";
				
				
            $_SESSION['schedchange']=2;
          
            echo '
               <table align="center" class="content" border="0" width="750" bordercolor="#000000">
       	  	<tr></tr>
       	  	<tr>
		<td></td>
		<td align="center">
               <input type="submit" name="submit" value="Submit" style="width:200px; height:30px">
	   	</td></table><br><br>
		  ';
			   
            include ('includes/footer.html');
        }
        else if (checkdayinternals())
	{
            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - INTERNAL CONFLICT 2', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);       

            echo '
                <h1 align="center">Conflict Page</h1>
                <br>
		<p align="center">
		<h3 align="center">
                There is an INTERNAL CONFLICT!
		<br>
		</p>
              <p align="center">A conflict has been detected in the change you have tried to make.
                <br>There are two major reasons for this.
	        <br>
	        <p>
                <br>
	        </p>
	        <p align="center">
	        <br>1.  You may have tried to make a change which conflicts with your own schedule
                for that day,
	        <br>
                <tab>    such as trying to schedule one assignment at the same time of another or
                selecting an end
                <br>         time prior to a begin time.
	        </p>
	        <br>
	        <p align="center">
	        <br>2.  You may have tried to make a change which conflicts with another partners
                assignment.
	        <br><tab>    If you are sharing an assignment with another partner, you must
                shorten their
	        <br><tab>    assignment first before adding the portion of the assignment you are
                working.
	        </p>
	        <br>
	        <p>
	        </p>
	        <p align="center">Please reevaluate your change.  If you do not know what the
                problem is, contact Dale Buchanan.
	        <br>To go back to the day assignment page, press the Submit button below.
	        </h3>
	        <br>
	        <br>
	          ';
            $_SESSION['schedchange']=2;
            echo '
                <center>
                <FORM METHOD="LINK" ACTION="day_display.php">
                <INPUT TYPE="submit" VALUE="Submit">
                </FORM>
	        <br>
                 ';
	    
            include ('includes/footer.html');
        }
	$_SESSION['check']=0; 
    }




    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

/*
 * SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3
 * SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3
 * SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3 SECTION 3
 */


    else if ($_SESSION['schedchange']==1)
    {
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//PRIMARY ASSIGNMENT PRIMARY ASSIGNMENT PRIMARY ASSIGNMENT PRIMARY ASSIGNMENT PRIMARY ASSIGNMENT//
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// IF THE PROGRAM IS RETURNING FROM "PRIMARY.PHP"                                               //
// IT HAS THE VALUE $_SESSION['changeassignment']==1.                                           //
// THIS MANAGES PRIMARY ASSIGNMENT CHANGES.                                                     //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        if ($_SESSION['changeassignment']==1)
	{
            //var_dump($_REQUEST['dayassignment1']);
            if ($_REQUEST['dayassignment1'] == 88) //88 is Weekday Vacation
            {
/*
 * Get who is making this change ($_SESSION['initials']).
 */                
                $s0 = "SELECT number
                       FROM   mds
                       WHERE  initials = '{$_SESSION['initials']}'";
                $q0 = mysql_query($s0);
                $a0 = mysql_fetch_row($q0);
                $mdno = $a0[0];
   
                $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
                $dyear = $datetime->format('Y');
                $dm = $datetime->format('n');
                $dd = $datetime->format('j');
   
/*
 * Get the old assignment 
 */
                
                $s1 = "SELECT assignment 
                       FROM   monthassignment
                       WHERE  mdnumber = {$mdno}
                       AND    monthnumber = {$dm}
                       AND    daynumber = {$dd}
                       AND    yearnumber = {$dyear}
                       AND    assigntype=1";
                $q1 = mysql_query($s1);
                $a1 = mysql_fetch_row($q1);
                $assig = $a1[0];
                
/*
 * If the person making the change is the ORMGR, etc. and he is trying to assign a weekday Vac,
 * warn him that this is not the proper way to assign unwanted vacation
 */                
                
                if (trim($assig) == 'ORMGR' || $_SESSION['initials']=='DB' 
                               || $_SESSION['initials']=='ST'
                               || $trim($assig) == 'ORMGR/Peds')
                {
?>
                                <script type="text/javascript">
                                alert("If you are assigning an unwanted/unscheduled vacation day\n"+
                                "this is NOT the way to do it!\n"+
                                "Please undo this change, go back, and\n"+
                                "review the correct procedure for assigning\n"+
                                "unwanted/unscheduled vacation days.");
                                </script>
<?php
                }
            }

/*
 * Select the current assignment for the person who the assignment is being changed.
 */

            $r333 = "SELECT *
		     FROM monthassignment
                     WHERE mdnumber={$_SESSION['mdn']}
		     AND monthnumber={$_SESSION['dtm']}
                     AND daynumber={$_SESSION['dai']}
                     AND yearnumber={$_SESSION['dty']}
                     AND assigntype=1
                     ORDER BY beginblock";
            $r222 = mysql_query($r333);

            
/*
 * Delete the current assignment from monthcal.
 */
            if (!empty($r222))
            {
                $rmc3 = "DELETE
                         FROM  monthcal
                         WHERE mdnumber={$_SESSION['mdn']}
		         AND   monthnumber={$_SESSION['dtm']}
			 AND   daynumber={$_SESSION['dai']}
			 AND   yearnumber={$_SESSION['dty']}
			 AND   assigntype=1";
                mysql_query($rmc3);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Delete all the existing assignments in TABLE MONTHASSIGNMENT and Backup these entries into   //
// the TABLE BUMONTHASSIGNMENT.  This gets MONTHASSIGNMENT ready to accept the new entries.     //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
                /*
                 * $r222 is the current assignment(s) for the person having the assignment 
                 * changed.
                 * Insert these into the BUMONTHASSIGNMENT table and then delete them from the 
                 * MONTHASSIGNMENT table.
                 */
                while ($bu = @mysql_fetch_row ($r222))
                {
                    $primarychange =   "INSERT INTO bumonthassignment ".
                                       "VALUES ( $bu[0], ".
                                                "$bu[1], ".
                                                "$bu[2], ".
                                                "$bu[3], ".
                                               "'$bu[4]', ".
                                                "$bu[5], ".
                                               "'$bu[6]', ".
                                                "$bu[7], ".
                                               "'$bu[8]', ".
                                                "$bu[9], ".
                                                "$bu[10], ".
                                               "'$bu[11]', ".
                                               "'$bu[12]', ".
                                                "NULL)";
   
$istring = str_replace("'","",$primarychange);
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                    
                    
                    mysql_query($primarychange);

                    $primarydelete = "DELETE
			              FROM   monthassignment
                                      WHERE  counter=$bu[13]";
  		    mysql_query($primarydelete);
                }
            }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for the first primary assignment.                                 //
// This is obtained from the page "primary.php"                                                 //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $pa1 = "SELECT assignment
		    FROM   assignments
                    WHERE  n={$_REQUEST['dayassignment1']}";
            $pa = mysql_query ($pa1);
            $paf = @mysql_fetch_row($pa);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for primary assignment 1.                                                 //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $begin_p1_time_period = $_REQUEST['btday1'];
            $getbeginp1time =   "SELECT time
                                 FROM   timeperiods
                                 WHERE  timeperiod = {$_REQUEST['btday1']}";

            $getbeginp1timequery = mysql_query($getbeginp1time);
            $begin_p1_time_array = @mysql_fetch_array($getbeginp1timequery);





/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for primary 1                                                               //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

            $end_p1_time_period = $_REQUEST['etday1'];

            $getendp1time = "  SELECT time
                               FROM   timeperiods
                               WHERE  timeperiod = {$_REQUEST['etday1']}";

            $getendp1timequery = mysql_query($getendp1time);
            $end_p1_time_array = @mysql_fetch_array($getendp1timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting NORMAL beginblock for primary 1 for updating MONTHCAL                                //
// $sqlhrs3a                                                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            $btet = "SELECT beginblock, endblock
                     FROM   assignments
                     WHERE  n={$_REQUEST['dayassignment1']}";
            $sqlhrs3q = mysql_query($btet);
            $sqlhrs3a = @mysql_fetch_row($sqlhrs3q);

/*
 * $paf[0] is the first primary assignment.
 * Insert into MONTHASSIGNMENT AND MONTHCAL the assignment and any deviance from the normal
 * hours for the assignment.
 * If the assignment is 'None' then no entry needs to be made?
 */
            if (trim($paf[0])!='None')
            {
                $homenew = "INSERT INTO monthassignment ".
		           "VALUES ({$_SESSION['mdn']}, ".
                                    "{$_SESSION['dtm']}, ".
                                    "{$_SESSION['dai']}, ".
                                    "{$_SESSION['dty']}, ".
                                    "'{$paf[0]}', ".
                                    "1, ".
                                    "'$begin_p1_time_array[0]', ".
                                    "$begin_p1_time_period, ".
                                    "'$end_p1_time_array[0]', ".
                                    "$end_p1_time_period, ".
                                    "{$_SESSION['weekend']}, ".
                                    "now(),".
                                    "'{$_SESSION['initials']}', ".
                                    "NULL)";
                                    
$istring = str_replace("'","",$homenew);            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                    
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: day_display.php\n".
                                "Statement: {$homenew}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                   
                                    
                mysql_query($homenew);

		$primarymcnew = "INSERT INTO monthcal".
		                " VALUES (   {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'{$paf[0]}', ".
                                            "1, ".
                                            "{$_SESSION['weekend']}, ".
                                            "'$begin_p1_time_array[0]', ".
                                            "'$end_p1_time_array[0]')";
                                            
$istring = str_replace("'","",$primarymcnew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                            
                mysql_query($primarymcnew);
            }
            else if (trim($paf[0])==='None')
            {
                $primarymcnew = "INSERT INTO monthcal".
		                " VALUES (   {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'{$paf[0]}', ".
                                            "1, ".
                                            "{$_SESSION['weekend']}, ".
                                            "'$begin_p1_time_array[0]', ".
                                            "'$end_p1_time_array[0]')";
$istring = str_replace("'","",$primarymcnew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                                                                      
                mysql_query($primarymcnew);
            }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// Managing day assignment 2  if it exists.                                                     //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            if (!empty($_REQUEST['btday2']))
            {
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for day assignment 2.                                             //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $pa1 = "SELECT assignment
		        FROM   assignments
			WHERE  n={$_REQUEST['dayassignment2']}";
                $pa = mysql_query ($pa1);
		$paf = @mysql_fetch_row($pa);
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for day assignment 2                                                      //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $begin_p2_time_period = $_REQUEST['btday2'];

                $getbeginp2time = "   SELECT time
                                      FROM   timeperiods
                                      WHERE  timeperiod = {$_REQUEST['btday2']}";

                $getbeginp2timequery = mysql_query($getbeginp2time);
                $begin_p2_time_array = @mysql_fetch_row($getbeginp2timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the day assignment 2                                                    //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $end_p2_time_period = $_REQUEST['etday2'];

                $getendp2time = "   SELECT time
                                    FROM   timeperiods
                                    WHERE  timeperiod = {$_REQUEST['etday2']}";

                $getendp2timequery = mysql_query($getendp2time);
                $end_p2_time_array = @mysql_fetch_row($getendp2timequery);
                
                
                
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting NORMAL beginblock for primary 2 for updating MONTHCAL                                //
// $sqlhrs3a                                                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $btet = "SELECT beginblock, endblock
                         FROM   assignments
                         WHERE  n={$_REQUEST['dayassignment2']}";
                $sqlhrs3q = mysql_query($btet);
                $sqlhrs3a = @mysql_fetch_row($sqlhrs3q);                

                if (trim($paf[0])!='None')
                {
                    $p2new =  " INSERT INTO monthassignment".
			      " VALUES ({$_SESSION['mdn']}, ".
                                        "{$_SESSION['dtm']}, ".
                                        "{$_SESSION['dai']}, ".
                                        "{$_SESSION['dty']}, ".
                                        "'{$paf[0]}', ".
                                        "1, ".
					"'$begin_p2_time_array[0]', ".
                                        "$begin_p2_time_period, ".
                                        "'$end_p2_time_array[0]', ".
					"$end_p2_time_period, ".
                                        "{$_SESSION['weekend']}, ".
                                        "now(), ".
                                        "'{$_SESSION['initials']}', ".
                                        "NULL)";
                                        
$istring = str_replace("'","",$p2new);                                        
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                        
                                        
                    $file = fopen("log.txt", "a") or exit("Unable to open file!");
                    $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                    $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                    "User: {$_SESSION['initials']}\n". 
                                    "Page: day_display.php\n".
                                    "Statement: {$p2new}\n\n";
                    fwrite($file, $logStatement);
                    fclose($file);                    
                                        
                    mysql_query($p2new);

                    
                    $mcq = "UPDATE monthcal ".
			   "SET    begintime = '$begin_p2_time_array[0]', ".
                                  "endtime = '$end_p2_time_array[0]' ".
                           "WHERE  mdnumber={$_SESSION['mdn']} ".
			   "AND    monthnumber={$_SESSION['dtm']} ".
                           "AND    daynumber={$_SESSION['dai']} ".
                           "AND    yearnumber={$_SESSION['dty']} ".
                           "AND    assignment='{$paf[0]}' ".
                           "AND    assigntype=1";

$istring = str_replace("'","",$mcq);                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Primary Change 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                            
                    mysql_query($mcq);
                }
            }
        }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOMECALL//HOME//
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// IF THE PROGRAM IS RETURNING FROM "HOME.PHP"                                                  //
// IT HAS THE VALUE $_SESSION['changeassignment']==3.                                           //
// THIS MANAGES HOME CALL and CALL CHANGES.                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
        else if ($_SESSION['changeassignment']==3)
	{ 
            //89 = Vac WE, 90 = Wkend -- REQUESTING ASSIGNMENTS
            if ($_REQUEST['callassignment1']==89 || $_REQUEST['callassignment1']==90)
            {
                $r333 = "SELECT   *
		         FROM     monthassignment 
                         WHERE    mdnumber={$_SESSION['mdn']}
		         AND      monthnumber={$_SESSION['dtm']} 
                         AND      daynumber={$_SESSION['dai']}
                         AND      yearnumber={$_SESSION['dty']}
                         AND      assigntype=3
                         ORDER BY beginblock";
                $r222 = mysql_query($r333);

                if (!empty($r222))
                {
                    $rmc3 = "DELETE
                             FROM   monthcal
                             WHERE  mdnumber={$_SESSION['mdn']}
                             AND    monthnumber={$_SESSION['dtm']} 
                             AND    daynumber={$_SESSION['dai']}
                             AND    yearnumber={$_SESSION['dty']}
                             AND    assigntype=3";
                    mysql_query($rmc3);

                    while ($bu = @mysql_fetch_row ($r222))
                    {
                        $homechange = "INSERT INTO bumonthassignment ".
                                      "VALUES ". 
                                           "( $bu[0], ".
                                             "$bu[1], ".
                                             "$bu[2], ".
                                             "$bu[3], ".
                                            "'$bu[4]', ".
                                             "$bu[5], ".
                                            "'$bu[6]', ".
                                             "$bu[7], ".
                                            "'$bu[8]', ".
                                             "$bu[9], ".
                                             "$bu[10], ".
                                            "'$bu[11]', ".
                                            "'$bu[12]', ".
                                             "NULL)";
                        mysql_query($homechange);

$istring = str_replace("'","",$homechange);                        
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Call Change 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                        
                        

                        $homedelete = "DELETE
                                       FROM   monthassignment 
                                       WHERE  counter=$bu[13]";
                        mysql_query($homedelete);
                    }
                }
            
                $btet = "SELECT beginblock, endblock, begintime, endtime
                         FROM   assignments
                         WHERE  n={$_REQUEST['callassignment1']}";
                $sqlhrs3q = mysql_query($btet);
                $sqlhrs3a = @mysql_fetch_row($sqlhrs3q);
                
                
                $s = "SELECT assignment
                      FROM   assignments
                      WHERE  n={$_REQUEST['callassignment1']}";
                $q = mysql_query($s);
                $a = mysql_fetch_row($q);
                $ass = $a[0];

                /*
                 * This insertion gets placed as a primary assignment since it is going from
                 * a weekend call assignment to a Vacation day or Wkend assignment.
                 */
                $homenew = "INSERT INTO monthassignment".
                           " VALUES ({$_SESSION['mdn']},".
                                    "{$_SESSION['dtm']},".
                                    "{$_SESSION['dai']},".
                                    "{$_SESSION['dty']},".
                                    "'$ass',".
                                    "1,".
                                    "'$sqlhrs3a[2]',".
                                    "$sqlhrs3a[0],".
                                    "'$sqlhrs3a[3]',".
                                    "$sqlhrs3a[1],".
                                    "{$_SESSION['weekend']},".
                                    "now(),".
                                    "'{$_SESSION['initials']}',".
                                    "NULL)";

$istring = str_replace("'","",$homenew);                                    
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                    
                                    
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: day_display.php\n".
                                "Statement: {$homenew}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                    
                                    
                $q = mysql_query($homenew);


                $primarymcnew = "INSERT INTO monthcal ".
                                " VALUES (   {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'$ass', ".
                                            "1, ".
                                            "{$_SESSION['weekend']}, ".
                                            "'$sqlhrs3a[2]', ". 
                                            "'$sqlhrs3a[3]')";
                                            
$istring = str_replace("'","",$primarymcnew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                            
                                            
                mysql_query($primarymcnew);
            }
            
            /*
             * If not assigning a weekend Vacation day or Wkend assignment --
             */
            else
            {
                
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Updating the TABLE MONTHASSIGNMENT.  Here HOME CALL is being managed.                        //
// First, insert the old assignment into bumonthassignment.                                     //
// Second, delete the old assignment from monthassignment and monthcal                          //
// Third, insert the new assignment into monthassignment and update monthcal                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            $r333 = "SELECT   *
		     FROM     monthassignment 
                     WHERE    mdnumber={$_SESSION['mdn']}
		     AND      monthnumber={$_SESSION['dtm']} 
                     AND      daynumber={$_SESSION['dai']}
                     AND      yearnumber={$_SESSION['dty']}
                     AND      assigntype=3
                     ORDER BY beginblock";
            $r222 = mysql_query($r333);

            if (!empty($r222))
            {
                $rmc3 = "DELETE
                         FROM   monthcal
                         WHERE  mdnumber={$_SESSION['mdn']}
		         AND    monthnumber={$_SESSION['dtm']} 
			 AND    daynumber={$_SESSION['dai']}
			 AND    yearnumber={$_SESSION['dty']}
                         AND    assigntype=3";
                mysql_query($rmc3);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Delete all the existing assignments in TABLE MONTHASSIGNMENT and Backup these entries into   //
// the TABLE BUMONTHASSIGNMENT.  This gets MONTHASSIGNMENT ready to accept the new entries.     //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
                while ($bu = @mysql_fetch_row ($r222))
                {
                    $homechange = "INSERT INTO bumonthassignment ".
			          "VALUES (  $bu[0], ".
                                            "$bu[1], ".
                                            "$bu[2], ".
                                            "$bu[3], ".
                                           "'$bu[4]', ".
                                            "$bu[5], ".
                                           "'$bu[6]', ".
                                            "$bu[7], ".
                                           "'$bu[8]', ".
                                            "$bu[9], ".
                                            "$bu[10], ".
                                           "'$bu[11]', ".
                                           "'$bu[12]', ".
                                            "NULL)";
                    mysql_query($homechange);
                    
$istring = str_replace("'","",$homechange);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                    

                    $homedelete = "DELETE
			           FROM   monthassignment 
                                   WHERE  counter=$bu[13]";
  		    mysql_query($homedelete);
                }
            }
		
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for home call.                                                    //
// This is obtained from the page "home.php"                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $pa1 = "SELECT   assignment
		    FROM     assignments 
                    WHERE    n={$_REQUEST['callassignment1']}";
                    
            $pa = mysql_query ($pa1);
            $paf = @mysql_fetch_row($pa);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for home call 1                                                           //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
            $begin_call_time_period = $_REQUEST['btcall1'];
            $getbegincalltime = "SELECT time
                                 FROM   timeperiods
                                 WHERE  timeperiod = {$_REQUEST['btcall1']}";

            $getbegincalltimequery = mysql_query($getbegincalltime);
            $begin_call_time_array = @mysql_fetch_array($getbegincalltimequery);





/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for home call 1                                                             //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

            $end_call_time_period = $_REQUEST['etcall1'];

            $getendcalltime = "SELECT time
                               FROM   timeperiods
                               WHERE  timeperiod = {$_REQUEST['etcall1']}";

            $getendcalltimequery = mysql_query($getendcalltime);
            $end_call_time_array = @mysql_fetch_array($getendcalltimequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting NORMAL beginblock for home call 1 for updating MONTHCAL                              //
// $sqlhrs3a                                                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            $btet = "SELECT beginblock, endblock
                     FROM   assignments
                     WHERE  n={$_REQUEST['callassignment1']}";
            $sqlhrs3q = mysql_query($btet);
            $sqlhrs3a = @mysql_fetch_row($sqlhrs3q);
		   
            if (trim($paf[0])!='None')
            {
                $homenew = "INSERT INTO monthassignment".
		           " VALUES ({$_SESSION['mdn']}, ".
                                    "{$_SESSION['dtm']}, ".
                                    "{$_SESSION['dai']}, ".
                                    "{$_SESSION['dty']}, ".
                                    "'{$paf[0]}', ".
                                    "3, ".
                                    "'$begin_call_time_array[0]', ".
                                    "$begin_call_time_period, ".
                                    "'$end_call_time_array[0]', ".
                                    "$end_call_time_period, ".
                                    "{$_SESSION['weekend']}, ".
                                    "now(), ".
                                    "'{$_SESSION['initials']}', ".
                                    "NULL)";
                                    
$istring = str_replace("'","",$homenew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                    
                                    
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: day_display.php\n".
                                "Statement: {$homenew}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                    
                                    
                mysql_query($homenew);
           
		$primarymcnew = "INSERT INTO monthcal ". 
		                "VALUES (    {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'{$paf[0]}', ".
                                            "3, ".
                                            "{$_SESSION['weekend']}, ".
                                            "'$begin_call_time_array[0]', ".
                                            "'$end_call_time_array[0]')";
                                            
$istring = str_replace("'","",$primarymcnew);                                            
$mdentryStatement = "INSERT INTO  mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                            
                                            
                mysql_query($primarymcnew);
            }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// Managing home call 2 if it exists.                                                           //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
            if (!empty($_REQUEST['btcall2']))
            {
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for home call.                                                    //
// This is obtained from the page "home.php"                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $pa1 = "SELECT assignment
		        FROM   assignments 
			WHERE  n={$_REQUEST['callassignment2']}";
                $pa = mysql_query ($pa1);
		$paf = @mysql_fetch_row($pa);
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for home call 2                                                           //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $begin_call2_time_period = $_REQUEST['btcall2'];

                $getbegincall2time = "SELECT time
                                      FROM   timeperiods
                                      WHERE  timeperiod = {$_REQUEST['btcall2']}";

                $getbegincall2timequery = mysql_query($getbegincall2time);
                $begin_call2_time_array = @mysql_fetch_row($getbegincall2timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for the home call 2                                                         //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
                $end_call2_time_period = $_REQUEST['etcall2'];

                $getendcall2time = "SELECT time
                                    FROM   timeperiods
                                    WHERE  timeperiod = {$_REQUEST['etcall2']}";

                $getendcall2timequery = mysql_query($getendcall2time);
                $end_call2_time_array = @mysql_fetch_row($getendcall2timequery);
			  
                if (trim($paf[0])!='None')
                {
                    $homenew = "INSERT INTO monthassignment".
			       " VALUES ({$_SESSION['mdn']}, ".
                                        "{$_SESSION['dtm']}, ".
                                        "{$_SESSION['dai']}, ".
                                        "{$_SESSION['dty']}, ".
                                        "'{$paf[0]}', ".
                                        "3, ".
					"'$begin_call2_time_array[0]', ".
                                        "$begin_call2_time_period, ".
                                        "'$end_call2_time_array[0]', ".
					"$end_call2_time_period, ".
                                        "{$_SESSION['weekend']}, ".
                                        "now(), ".
                                        "'{$_SESSION['initials']}', ".
                                        "NULL)";
                                        
$istring = str_replace("'","",$homenew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                        
                                        
                    $file = fopen("log.txt", "a") or exit("Unable to open file!");
                    $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                    $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                    "User: {$_SESSION['initials']}\n". 
                                    "Page: day_display.php\n".
                                    "Statement: {$homenew}\n\n";
                    fwrite($file, $logStatement);
                    fclose($file);                    
                                        
                    mysql_query($homenew);

                    $mcq =  "UPDATE monthcal ".
			    "SET    begintime = '$begin_call2_time_array[0]', ".
                            "       endtime = '$end_call2_time_array[0]' ".
                            "WHERE  mdnumber={$_SESSION['mdn']} ".
			    "AND    monthnumber={$_SESSION['dtm']} ".
                            "AND    daynumber={$_SESSION['dai']} ".
                            "AND    yearnumber={$_SESSION['dty']} ".
                            "AND    assignment='{$paf[0]}' ".
                            "AND    assigntype=3";
                            
$istring = str_replace("'","",$mcq);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - Home Change 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                            
                            
                    mysql_query($mcq);
                }
            }
            }
        }
  



        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//ADDEDHOURS//
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// IF THE PROGRAM IS RETURNING FROM "ADD.PHP" IT HAS THE VALUE $_SESSION['changeassignment']==4.//
// THIS MANAGES ADDED HOUR CHANGES.                                                             //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
        else if ($_SESSION['changeassignment']==4)
	{
            if ($_SESSION['specialadd']==1 && (isset($_SESSION['reasonaddedhours1']))
                    && 
                    (
/*
 * These need to be changed.??????????????????????????????????????????????????????????????????????
 */
                        /*
                         * Want to make it clear that when someone is on C OH almost all of their
                         * hours should be added as C OH hours.
                         */
                        ($_SESSION['reasonaddedhours1']!=55) //C OH WD
                        && 
                        ($_SESSION['reasonaddedhours1']!=56) //C OH WE
                        &&
                        ($_SESSION['reasonaddedhours1']!=62) //None WD
                        &&
                        ($_SESSION['reasonaddedhours1']!=63) //None WE
                     )
               )
            {
?>
                <script type="text/javascript">
                alert("You are on Ortho Home Call.\n"+
                "You would normally use C OH as the reason for adding hours."+
                "\nPlease make sure you want to choose OTHER.");
                </script>
<?php
                $_SESSION['specialadd']=0;
            }
	    else if ($_SESSION['specialadd']==2 && (isset($_SESSION['reasonaddedhours1']))
                     && 
                     (
                        /*
                         * Want to make it clear that when someone is on Peds Call almost all of
                         * their hours should be added as Peds Call hours.
                         */
                          ($_SESSION['reasonaddedhours1']!=67) //Peds Call WD
                          && 
                          ($_SESSION['reasonaddedhours1']!=68) //Peds Call WE
                          &&
                          ($_SESSION['reasonaddedhours1']!=62) //None WD
                          &&
                          ($_SESSION['reasonaddedhours1']!=63) //None WE
                     )
                )
            {
?>
                <script type="text/javascript">
    		alert("You are on Peds Home Call.\n"+
    	    	"If you are adding hours for Peds Coverage,\n"+
    	    	"please go back and select \"Peds\" as the reason you are adding hours.");
    		</script>
<?php
		$_SESSION['specialadd']=0;
            }
	    else if ($_SESSION['specialadd']==3 && (isset($_SESSION['reasonaddedhours1']))
                     &&
                     (
                        /*
                         * Want to make it clear that when someone is on H 1 almost all of
                         * their hours should be added as Heart Call hours.
                         */
                        ($_SESSION['reasonaddedhours1']!=58) //H 1 WD
		 	&& 
		 	($_SESSION['reasonaddedhours1']!=59) //H 1 WE
		 	&&
		 	($_SESSION['reasonaddedhours1']!=62) //None WD
                        &&
                        ($_SESSION['reasonaddedhours1']!=63) //None WE
                     )
                    )
            {
?>
		<script type="text/javascript">
    		alert("You are on Heart Home Call.\n"+
    	    	"If you are adding hours for Heart Coverage,\n"+
    	    	"please go back and select \"H 1\" as the reason you are adding hours.");
    		</script>
<?php
		$_SESSION['specialadd']=0;
            }
                       
            
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Updating the TABLE MONTHASSIGNMENT.  Here ADDED HOURS is being managed.                      //
// First, insert the old assignment into bumonthassignment.                                     //
// Second, delete the old assignment from monthassignment.                                      //
// Third, insert the new assignment into monthassignment.                                       //
////////////////////////////////////////////////////////////////////////////////////////////////// 
 */
            $hrsmc=0.0;
            $r333 = "SELECT * 
                     FROM monthassignment 
                     WHERE mdnumber={$_SESSION['mdn']} 
                     AND monthnumber={$_SESSION['dtm']} 
                     AND daynumber={$_SESSION['dai']}
                     AND yearnumber={$_SESSION['dty']} 
                     AND assigntype=4";
            $r222 = mysql_query($r333);
            if (!empty($r222))
            {
                $rmc3 = "DELETE 
                         FROM monthcal 
                         WHERE mdnumber={$_SESSION['mdn']} 
                         AND monthnumber={$_SESSION['dtm']} 
                         AND daynumber={$_SESSION['dai']} 
                         AND yearnumber={$_SESSION['dty']} 
                         AND assigntype=4";
                mysql_query($rmc3);
		   
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Delete all the existing add hours in the TABLE MONTHASSIGNMENT and Backup these entries into //
// the TABLE BUMONTHASSIGNMENT.  This gets MONTHASSIGNMENT ready to accept the new entries.     //
////////////////////////////////////////////////////////////////////////////////////////////////// 
 */
                while ($bu = @mysql_fetch_row ($r222))
                {
                    $homechange =  "INSERT INTO bumonthassignment ".
                                   "VALUES ( $bu[0], ".
                                            "$bu[1], ".
                                            "$bu[2], ".
                                            "$bu[3], ".
                                           "'$bu[4]', ".
                                            "$bu[5], ".
                                           "'$bu[6]', ".
                                            "$bu[7], ".
                                           "'$bu[8]', ".
                                            "$bu[9], ".
                                            "$bu[10], ".
                                           "'$bu[11]', ".
                                           "'$bu[12]', ".
                                            "NULL)";
                    
$istring = str_replace("'","",$homechange);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                    
                    mysql_query($homechange);

                    $homedelete = "DELETE 
                                   FROM monthassignment 
                                   WHERE counter=$bu[13]";
                    mysql_query($homedelete);
               }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for the reason new added hours 1 is added.                        //
// This is obtained from the page "add.php"                                                     //
////////////////////////////////////////////////////////////////////////////////////////////////// 
*/
    if (isset($_SESSION['reasonaddedhours1'])) {
                $pa1 = "SELECT assignment 
                        FROM assignments 
                        WHERE n={$_SESSION['reasonaddedhours1']}";
                $pa = mysql_query ($pa1);
                $paf = @mysql_fetch_row($pa);

/*            
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for added hours 1                                                         //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/

                $begin_hours1_time_period = $_SESSION['btaddedhours1'];

                $getbeginhours1time = "SELECT time
                                       FROM timeperiods
                                       WHERE timeperiod = {$_SESSION['btaddedhours1']}";

                $getbeginhours1timequery = mysql_query($getbeginhours1time);
                $begin_hours1_time_array = @mysql_fetch_row($getbeginhours1timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for added hours 1                                                           //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/

                $end_hours1_time_period = $_SESSION['etaddedhours1'];

                $getendhours1time =   "SELECT time
                                       FROM timeperiods
                                       WHERE timeperiod = {$_SESSION['etaddedhours1']}";

                $getendhours1timequery = mysql_query($getendhours1time);
                $end_hours1_time_array = @mysql_fetch_row($getendhours1timequery);

                if (trim($paf[0])!='None')
                {
                    $homenew = "INSERT INTO monthassignment". 
                               " VALUES (    {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'{$paf[0]}', ".
                                            "4, ".
                                            "'$begin_hours1_time_array[0]', ".
                                            "$begin_hours1_time_period, ".
                                            "'$end_hours1_time_array[0]', ".
                                            "$end_hours1_time_period, ".
                                            "{$_SESSION['weekend']}, ".
                                            "now(), ". 
                                            "'{$_SESSION['initials']}', ".
                                            "NULL)";
                                            
$istring = str_replace("'","" ,$homenew);  
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                            
                                           
                    $file = fopen("log.txt", "a") or exit("Unable to open file!");
                    $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                    $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                    "User: {$_SESSION['initials']}\n". 
                                    "Page: day_display.php\n".
                                    "Statement: {$homenew}\n\n";
                    fwrite($file, $logStatement);
                    fclose($file);                       
                                           
                    mysql_query($homenew);

                    $homemc =  "INSERT INTO monthcal ".
                               "VALUES (     {$_SESSION['mdn']}, ".
                                            "{$_SESSION['dtm']}, ".
                                            "{$_SESSION['dai']}, ".
                                            "{$_SESSION['dty']}, ".
                                            "'AH: {$paf[0]}', ".
                                            "4, ".
                                            "{$_SESSION['weekend']}, ".
                                            "'$begin_hours1_time_array[0]', ".
                                            "'$end_hours1_time_array[0]')";
                                            
$istring = str_replace("'","",$homemc);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 1 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                            
                    mysql_query($homemc);
                }
    }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// Managing added hours 2 if it exists.                                                         //
////////////////////////////////////////////////////////////////////////////////////////////////// 
 */
                if (isset($_SESSION['reasonaddedhours2']))
                {
                    if ($_SESSION['specialadd']==1 
                        && 
                        (
/*
 * These need to be changed.??????????????????????????????????????????????????????????????????????
 */
/*
* Want to make it clear that when someone is on C OH almost all of their
* hours should be added as C OH hours.
*/
                            ($_SESSION['reasonaddedhours1']!=55) //C OH WD
                            &&
                            ($_SESSION['reasonaddedhours1']!=56) //C OH WE
                            &&
                            ($_SESSION['reasonaddedhours1']!=62) //None WD
                            &&
                            ($_SESSION['reasonaddedhours1']!=63) //None WE
                         )
                       )
                    {
?>
                        <script type="text/javascript">
                        alert("You are on Ortho Home Call.\n"+
                        "You would normally use C OH as the reason for adding hours."+
                        "\nPlease make sure you want to choose OTHER.");
                        </script>
<?php
                        $_SESSION['specialadd']=0;
                    }
                    else if ($_SESSION['specialadd']==2 
                             && 
                             (
/*
 * Want to make it clear that when someone is on Peds Call almost all of
 * their hours should be added as Peds Call hours.
 */
                                  ($_SESSION['reasonaddedhours1']!=67) //Peds Call WD
                                  &&
                                  ($_SESSION['reasonaddedhours1']!=68) //Peds Call WE
                                  &&
                                  ($_SESSION['reasonaddedhours1']!=62) //None WD
                                  &&
                                  ($_SESSION['reasonaddedhours1']!=63) //None WE
                             )
                        )
                    {
?>
                        <script type="text/javascript">
                        alert("You are on Peds Home Call.\n"+
                        "If you are adding hours for Peds Coverage,\n"+
                        "please go back and select \"Peds\" as the reason you are adding hours.");
                        </script>
<?php
                        $_SESSION['specialadd']=0;
                    }
                    else if ($_SESSION['specialadd']==3 
                             && 
                             (
/*
 * These need to be changed.??????????????????????????????????????????????????????????????????????
 */
/*
* Want to make it clear that when someone is on H 1 almost all of
* their hours should be added as Heart Call hours.
*/
                                ($_SESSION['reasonaddedhours1']!=58) //H 1 WD
                                &&
                                ($_SESSION['reasonaddedhours1']!=59) //H 1 WE
                                &&
                                ($_SESSION['reasonaddedhours1']!=62) //None WD
                                &&
                                ($_SESSION['reasonaddedhours1']!=63) //None WE
                             )
                            )
                    {
?>
                        <script type="text/javascript">
                        alert("You are on Heart Home Call.\n"+
                        "If you are adding hours for Heart Coverage,\n"+
                        "please go back and select \"H 1\" as the reason you are adding hours.");
                        </script>
<?php
                        $_SESSION['specialadd']=0;
                    }
            
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Updating the TABLE MONTHASSIGNMENT.  Here ADDED HOURS is being managed.                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
                    $hrsmc=0.0;


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for the reason new added hours 2 is added.                        //
// This is obtained from the page "add.php"                                                     //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
                        $pa1 = "SELECT assignment 
                                FROM assignments 
                                WHERE n={$_SESSION['reasonaddedhours2']}";
                        $pa = mysql_query ($pa1);
                        $paf = @mysql_fetch_row($pa);

/*            
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for added hours 2                                                         //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/


                        $begin_hours2_time_period = $_SESSION['btaddedhours2'];

                        $getbeginhours2time = "SELECT time
                                               FROM timeperiods
                                               WHERE timeperiod = {$_SESSION['btaddedhours2']}";

                        $getbeginhours2timequery = mysql_query($getbeginhours2time);
                        $begin_hours2_time_array = @mysql_fetch_row($getbeginhours2timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for added hours 2                                                           //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/


                        $end_hours2_time_period = $_SESSION['etaddedhours2'];

                        $getendhours2time =   "SELECT time
                                               FROM timeperiods
                                               WHERE timeperiod = {$_SESSION['etaddedhours2']}";

                        $getendhours2timequery = mysql_query($getendhours2time);
                        $end_hours2_time_array = @mysql_fetch_row($getendhours2timequery);

                        if (trim($paf[0])!='None')
                        {
                            $homenew = "INSERT INTO monthassignment". 
                                       " VALUES (    {$_SESSION['mdn']}, ".
                                                    "{$_SESSION['dtm']}, ".
                                                    "{$_SESSION['dai']}, ".
                                                    "{$_SESSION['dty']}, ".
                                                    "'{$paf[0]}', ".
                                                    "4, ".
                                                    "'$begin_hours2_time_array[0]', ".
                                                    "$begin_hours2_time_period, ".
                                                    "'$end_hours2_time_array[0]]', ".
                                                    "$end_hours2_time_period, ".
                                                    "{$_SESSION['weekend']}, ".
                                                    "now(), ". 
                                                    "'{$_SESSION['initials']}', ".
                                                    "NULL)";
                                                    
$istring = str_replace("'","",$homenew);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                                    
                                                   
                            $file = fopen("log.txt", "a") or exit("Unable to open file!");
                            $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                            $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                            "User: {$_SESSION['initials']}\n". 
                                            "Page: day_display.php\n".
                                            "Statement: {$homenew}\n\n";
                            fwrite($file, $logStatement);
                            fclose($file);                       
                                                   
                            mysql_query($homenew);

                            $homemc =  "INSERT INTO monthcal ". 
                                       "VALUES (     {$_SESSION['mdn']}, ".
                                                    "{$_SESSION['dtm']}, ".
                                                    "{$_SESSION['dai']}, ".
                                                    "{$_SESSION['dty']}, ".
                                                    "'AH: {$paf[0]}', ".
                                                    "4, ".
                                                    "{$_SESSION['weekend']}, ".
                                                    "'$begin_hours2_time_array[0]', ".
                                                    "'$end_hours2_time_array[0]]' ".    
                                                    "   )";
                                                    
$istring = str_replace("'","",$homemc);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 2 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                                    
                            mysql_query($homemc);
                        }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// Managing added hours 3 if it exists.                                                         //
//                                                                                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
                        if (isset($_SESSION['reasonaddedhours3']))
                        {
                            if ($_SESSION['specialadd']==1 
                                AND 
                                (
/*
 * Want to make it clear that when someone is on C OH almost all of their
 * hours should be added as C OH hours.
 */
                                    ($_SESSION['reasonaddedhours1']!=55) //C OH WD
                                    &&
                                    ($_SESSION['reasonaddedhours1']!=56) //C OH WE
                                    &&
                                    ($_SESSION['reasonaddedhours1']!=62) //None WD
                                    &&
                                    ($_SESSION['reasonaddedhours1']!=63) //None WE
                                  )
                               )
                            {
?>
                                <script type="text/javascript">
                                alert("You are on Ortho Home Call.\n"+
                                "You would normally use C OH as the reason for adding hours."+
                                "\nPlease make sure you want to choose OTHER.");
                                </script>
<?php
                                $_SESSION['specialadd']=0;
                            }
                            else if ($_SESSION['specialadd']==2 
                                     AND 
                                     (
/*
* Want to make it clear that when someone is on Peds Call almost all of
* their hours should be added as Peds Call hours.
*/
                                          ($_SESSION['reasonaddedhours1']!=67) //Peds Call WD
                                          &&
                                          ($_SESSION['reasonaddedhours1']!=68) //Peds Call WE
                                          &&
                                          ($_SESSION['reasonaddedhours1']!=62) //None WD
                                          &&
                                          ($_SESSION['reasonaddedhours1']!=63) //None WE
                                     )
                                    )
                            {
?>
                                <script type="text/javascript">
                                alert("You are on Peds Home Call.\n"+
                                "If you are adding hours for Peds Coverage,\n"+
                                "please go back and select \"Peds\" as the reason you are \n"+
                                "adding hours.");
                                </script>
<?php
                                $_SESSION['specialadd']=0;
                            }
                            else if ($_SESSION['specialadd']==3 
                                     AND 
                                     (
/*
* Want to make it clear that when someone is on H 1 almost all of
* their hours should be added as Heart Call hours.
*/
                                        ($_SESSION['reasonaddedhours1']!=58) //H 1 WD
                                        &&
                                        ($_SESSION['reasonaddedhours1']!=59) //H 1 WE
                                        &&
                                        ($_SESSION['reasonaddedhours1']!=62) //None WD
                                        &&
                                        ($_SESSION['reasonaddedhours1']!=63) //None WE
                                     )
                                    )
                            {
?>
                                <script type="text/javascript">
                                alert("You are on Heart Home Call.\n"+
                                "If you are adding hours for Heart Coverage,\n"+
                                "please go back and select \"H 1\" as the reason you are \n"+
                                "adding hours.");
                                </script>
<?php
                                $_SESSION['specialadd']=0;
                            }
            
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Updating the TABLE MONTHASSIGNMENT.  Here ADDED HOURS is being managed.                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
                            $hrsmc=0.0;


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Get the correct assignment for the reason new added hours 3 is added.                        //
// This is obtained from the page "add.php"                                                     //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
                                $pa1 = "SELECT assignment 
                                        FROM assignments 
                                        WHERE n={$_SESSION['reasonaddedhours3']}";
                                $pa = mysql_query ($pa1);
                                $paf = @mysql_fetch_row($pa);

/*            
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting beginblock for added hours 1                                                         //
// $rbt                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

                        $begin_hours3_time_period = $_SESSION['btaddedhours3'];

                        $getbeginhours3time = "SELECT time
                                               FROM timeperiods
                                               WHERE timeperiod = {$_SESSION['btaddedhours3']}";

                        $getbeginhours3timequery = mysql_query($getbeginhours3time);
                        $begin_hours3_time_array = @mysql_fetch_row($getbeginhours3timequery);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// Getting endblock for added hours 1                                                           //
// $ret                                                                                         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */

                        $end_hours3_time_period = $_SESSION['etaddedhours3'];

                        $getendhours3time =   "SELECT time
                                               FROM timeperiods
                                               WHERE timeperiod = {$_SESSION['etaddedhours3']}";

                        $getendhours3timequery = mysql_query($getendhours3time);
                        $end_hours3_time_array = @mysql_fetch_row($getendhours3timequery);

                                if (trim($paf[0])!='None')
                                {
                                    $homenew = "INSERT INTO monthassignment". 
                                               " VALUES (    {$_SESSION['mdn']}, ".
                                                            "{$_SESSION['dtm']}, ".
                                                            "{$_SESSION['dai']}, ".
                                                            "{$_SESSION['dty']}, ".
                                                            "'{$paf[0]}', ".
                                                            "4, ".
                                                            "'$begin_hours3_time_array[0]', ".
                                                            "$begin_hours3_time_period, ".
                                                            "'$end_hours3_time_array[0]]', ".
                                                            "$end_hours3_time_period, ".
                                                            "{$_SESSION['weekend']}, ".
                                                            "now(), ". 
                                                            "'{$_SESSION['initials']}', ".
                                                            "NULL)";

$istring = str_replace("'","",$homenew);                                                           
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                                            
                                                           
                                    $file = fopen("log.txt", "a") or exit("Unable to open file!");
                                    $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                                    $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                                    "User: {$_SESSION['initials']}\n". 
                                                    "Page: day_display.php\n".
                                                    "Statement: {$homenew}\n\n";
                                    fwrite($file, $logStatement);
                                    fclose($file);                       
                                                           
                                    mysql_query($homenew);

                                    $homemc =  "INSERT INTO monthcal ".
                                               "VALUES (     {$_SESSION['mdn']}, ".
                                                            "{$_SESSION['dtm']}, ".
                                                            "{$_SESSION['dai']}, ".
                                                            "{$_SESSION['dty']}, ".
                                                            "'AH: {$paf[0]}', ".
                                                            "4, ".
                                                            "{$_SESSION['weekend']}, ".
                                                            "'$begin_hours3_time_array[0]', ".
                                                            "'$end_hours3_time_array[0]]' ".
                                                            " )";
$istring = str_replace("'","",$homemc);                                            
$mdentryStatement = "INSERT INTO mdlog ".
"VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'day_display.php - AH 3 - {$istring}', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);                                    
                                                            
                                    mysql_query($homemc);
                                }
                        }
                }
            }
        }
        unset($_SESSION['reasonaddedhours1']);
        unset($_SESSION['ahr']);
        day_display_code();
    }


/*
 * SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4
 * SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4
 * SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4 SECTION 4
 */
    else
    {
        if ($_SESSION['schedchange']==0)
            $_SESSION['dai']=$_REQUEST['dai'];
   
        day_display_code();
    }
}

?>
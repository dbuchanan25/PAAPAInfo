<?php

session_start();
/*
 * Version 02_02
 * Previous page:  Any page with menu items
 * Next page:  Any page with menu items
 */
/*
 * Last Revised 2014-01-08
 */
/*
 * Revised 2014-01-05/08 to be able to display each assignment and its start and
 * stop times instead of hour deviations.
 * Also, if more information than is allowed by the display is present, the 
 * background is converted to red to indicate there is incomplete data.
 * Better formatting of the information.
 * Based on the MySQL table "monthcal" in the database "anesthesiapay"
 * monthcal
Column	Type	Null	Default 	Comments 	MIME
mdnumber 	int(11)	Yes 	NULL  	  	 
monthnumber 	int(11)	Yes 	NULL  	  	 
daynumber 	int(11)	Yes 	NULL  	  	 
yearnumber 	int(11)	Yes 	NULL  	  	 
assignment 	varchar(20)	Yes 	NULL  	  	 
assigntype 	int(11)	Yes 	NULL  	  	 
weekend 	int(11)	Yes 	NULL  	  	 
begintime 	varchar(15)	Yes 	NULL  	  	 
endtime 	varchar(15)	No 	  	  	 
 */
/*
 * Revised 2011-08-24 to decrease the font size to 8 to fit in assignments more easily into the
 * table <td> and to reformat the code for easier reading.
 */
/*
   This function is called from "monthview.php" to fill in the specifics
   of the day blocks on the COMPLETE MONTH page.
*/
/*
   ---VARIABLES---
   $mda			  :	number of the physician of the schedule
   $sqlmq		  :	array of information from the table "monthcal" including
   				daynumber, assignment, assigntype, weekend, hours for a particular
				physician for the month
   $sqlma		  :	each row of $sqlmq
   $mdassign              :	a 2-D array of assignments for the month
   
*/
if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
} //end if

/*
   If the user is logged in then the program skips to here.
*/
/*
   ---VARIABLES---
   $dimo                    :       Contains the number of days of the month being displayed
   $_SESSION['dimo']        :       Session variable containing the same information
   $_SESSION['dtm']         :       The number of this month
   $_SESSION['dty']         :       The number of this year
   $_SESSION['schedmd']     :       Session variable containing the initials
                                    of the physician of the schedule
   $schedmdnumber           :       Result from query containing the number of the $_SESSION['schedmd']
   $_SESSION['schedmdnum']  :       Session variable containing the number of the physician
   $for_row                 :       First and Last name of the physician from $_SESSION['schedmd']
   $_SESSION['initials']    :       Session variable containing the initials of the user physician
                                    (the physician who is logged on)
   $frow                    :       Contains the first and last names of the user physician
   $sqlmdq                  :       Is an array which contains the number and last name of all
                                    physicians ordered by number.
   $sqlmdf                  :       Contains each row of $sqlmdq
*/
else
{
   $page_title = 'Month Schedule';
   echo '<title>'.$page_title.'</title>';
   require_once ($_SESSION['login2string']);
   include ('includes/header.php');

   //Get the number of days in this month.
   $dimo = cal_days_in_month(CAL_GREGORIAN, $_SESSION['dtm'], $_SESSION['dty']) ;
   //$_SESSION['dimo'] holds the number of days in this month
   $_SESSION['dimo']=$dimo;
  
  $r = "SELECT number
        FROM mds
        WHERE initials='{$_SESSION['schedmd']}'";
  $r1 = mysql_query($r);
  $schedmdnumber = @mysql_fetch_row($r1);
  //$_SESSION['schedmdnum'] holds the number of the current md 
  $_SESSION['schedmdnum']=$schedmdnumber[0];
   
  
  $test = $_SESSION['initials'];
  $qu = "   SELECT first, last
            FROM mds
            WHERE initials='$test'";
  $firstlast = mysql_query($qu);
  $frow = @mysql_fetch_row($firstlast);
  

  /*
   * menu at the top of the page for redirection and to identify the user
   */

  include_once 'menuBar.php';
  menuBar(5663);
  
/*
 * Shows the correct month and year
 */
  echo "<table class='table5'>
            <tr>
                <td>
                    <center>
                    <h3>
                        Schedule For: {$_SESSION['dtm']}/{$_SESSION['dty']}
                </td>
            </tr>
        </table>";
 
  echo '<br>
        <br>';

  
  /*
   * Selecting each of the anesthesiologists from the table "mds"
   * Ordering them by number
   */
  $sqlmd = "SELECT DISTINCT number, last, first
            FROM mds
            WHERE number < 900
            ORDER BY last, first";
  $sqlmd_number_last = mysql_query($sqlmd);

  /*
   * Width of the page is 1650 px.
   * Width of the first column is 150 px / contains name and number of anesthesiologist
   * Width of the next $_SESSION['dimo'] columns is 75 px
   */
  
  echo '<table bgcolor=black style="width:2625px;">';
  echo '    <tr>
                <td style="width:150px;" >
                </td>';

  /*
   * Going through and printing each day of the month across the top of the page
   */
  for ($xd=1; $xd<=$_SESSION['dimo']; $xd++)
  {
     echo '     <td style="width:75px; font-size:smaller; color:white;" align="center">
                <b>
	          '.$xd.'
                </b>
                </td>';
  }
  echo '    </tr>';
 
  
  /*
   * $dateline keeps count of the number of lines displayed on the page.
   * After every 7th line another line containing the dates is displayed.
   */
  $dateline=0;
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  /*
   * End of preliminaries and one-time informational
   * display at the top of the
   * page.  From here the program will continue to cycle through the 
   * anesthesiologists, then each day of the month for a particular 
   * anesthesiologist, to display all assignments for all anesthesiologists
   * for this month.
   */
  /*
   * The program goes through each anesthesiologist and displays their
   * assignments on each row of the table
   * 
   * DESIGNATE: MONTHDISPLAY
   * 
   */
  while ($sqlmdf = mysql_fetch_array($sqlmd_number_last))
  {
      
      
    /*
     * checks to see if a repeat date line needs to be displayed
     * if it does, then the line of days is constructed and displayed
     * then $dateline is reset to 0 for a new count
     */
    if ($dateline === 7)
    {
        echo ' <tr>
                    <td style="width:150px">
                    </td>';
			  
        for ($xd=1; $xd<=$_SESSION['dimo']; $xd++)
        {
            echo '  <td style="width:75px; font-size:smaller; color:white" align="center"><b>
                    '.$xd.'
                    </b></td>';
        } //end for
        echo '  </tr>';
        /*
         * resets the variable $dateline to 0
         */
        $dateline=0;
   } //end if ($dateline === 7)
   
   
   /*
    * displays the last name of the anesthesiologist and their number in
    * the first cell of the table on this row
    */
     echo ' <tr>
                <td bgcolor="white" width="150" height="50" align="center">
                    <table cellspacing="0" cellpadding="0">
                        <tr>
                            <td height="20" bgcolor="white" width="148" align="center"
                                style="font-size:small;">
                            <b>';
	echo                    $sqlmdf['first'].' '.$sqlmdf['last'];
	echo '              </b>
                            </td>
                        </tr>
                        <tr>
                        </tr>';
                        
//                        <tr>
//                            <td height="20" bgcolor="white" width="148" align="center"
//                                style="font-size:small;">';
//	echo                    $sqlmdf['number'];
//	echo '              </td>
//                        </tr>
        echo '
                    </table>
              </td>';
	
        
   /*
    * Selects the current monthly assignments, etc. for this anesthesiologist
    * for each day of the month and puts it into an array of 17 variables for
    * each day of the month.
    */     
   $sqlmonth = "SELECT daynumber, assignment, assigntype, weekend, begintime, endtime
                FROM monthcal
                WHERE mdnumber={$sqlmdf['number']}
                AND monthnumber={$_SESSION['dtm']}
                AND yearnumber={$_SESSION['dty']}
                ORDER BY daynumber, assigntype, begintime";
   $sqlmq = mysql_query($sqlmonth);
   $dayindex=0;
   
   /*
    * If someone is new and/or doesn't have any assignments then they will
    * be skipped and a black space will occupy the calendar.
    */
   
   for ($x = 1; $x <= $_SESSION['dimo']; $x++)
   {
       $mdassign[$x] = array_fill(0,17,null);
       $mdassign[$x]['overflow'] = 0;
   } //end for
   
   /*
    * Setup the array to include all the assignments and times for the month
    * designated by monthnumber={$_SESSION['dtm']} above for all the 
    * anesthesiologists.
    * DESIGNATE:  CURRENTMONTHARRAY
    */
   if (mysql_num_rows($sqlmq) > 0)
   {
       /*
        * Set up an array with 17 variables for each day.
        * Initiate the assignment for the day to 'None' as a filler.
        */
       
        /*
         * array array_fill ( int $start_index , int $num , mixed $value )
         * Fills an array with num entries of the value of the value parameter, 
         * keys starting at the start_index parameter. 
         * 
         */
       for ($x = 1; $x <= $_SESSION['dimo']; $x++)
       {
           $mdassign[$x] = array_fill(0,17,null);
           $mdassign[$x]['overflow'] = 0;
       } //end for
       
        
       while ($sqlma = @mysql_fetch_array($sqlmq))
       {
           /*
            * $sqlma['daynumber']!=$dayindex
            * it won't be if it is a new day and group of assignments,
            * it will be if it is a continuation of the same day
            * If it is a new day then $dayindex gets assigned the day number and the
            * array $mdassign gets intialized to all 0's
            */
           
           
            if ($sqlma['daynumber']!=$dayindex)
            {
                $dayindex=$sqlma['daynumber'];
            } //end if
            
            /*
             * $mdassign[day][0]  = weekend status
             * $mdassign[day][1]  = dayassignment1
             * $mdassign[day][2]  = begintime dayassignment1
             * $mdassign[day][3]  = endtime dayassignment1
             * $mdassign[day][4]  = dayassignment2
             * $mdassign[day][5]  = begintime dayassignment2
             * $mdassign[day][6]  = endtime dayassignment2
             * $mdassign[day][7]  = callassignment1
             * $mdassign[day][8]  = begintime callassignment1
             * $mdassign[day][9]  = endtime callassignment1
             * $mdassign[day][10] = callassignment2
             * $mdassign[day][11] = begintime callassignment2
             * $mdassign[day][12] = endtime callassignment2
             * $mdassign[day][13] = addedhour1 begintime
             * $mdassign[day][14] = addedhour1 endtime
             * $mdassign[day][15] = addedhour2 begintime
             * $mdassign[day][16] = addedhour2 endtime
             * $mdassign[day][17] = overflow  - initialized to 0, if 1 it means
             *                                  there are too many assignments
             *                                  to adequately display (ie 3
             *                                  day assignments, 3 call assign-
             *                                  ments, or 3 add hour assignments)
             */
            $mdassign[$sqlma['daynumber']]['weekend']=$sqlma['weekend'];

            /*
             * $sqlma[2] is the assigntype 1=day 3=call 4=added hours
             */
            if ($sqlma['assigntype']==='1')
            {
                /*
                 * checking to see if the array has been changed from the original
                 * initialization to 0
                 * if not, and $sqlma[2] ($sqlma['assigntype']) is 1, a day1 
                 * assignment is contained and get put in $mdassign[$sqlma[0]][0]
                 * and $mdassign[$sqlma[0]][1]
                 * if $mdassign[$sqlma['daynumber']][0]==0 is not true, then this
                 * is a day2 assignment and gets put in $mdassign[$sqlma[0]][3] and
                 * $mdassign[$sqlma[0]][4]
                 */
                if (!isset($mdassign[$sqlma['daynumber']]['assignment1']))
                {
                    $mdassign[$sqlma['daynumber']]['assignment1']=$sqlma['assignment'];
                    $mdassign[$sqlma['daynumber']]['a1begintime']=substr($sqlma['begintime'], 0, 5);
                    $mdassign[$sqlma['daynumber']]['a1endtime']=substr($sqlma['endtime'], 0, 5);
                } //end if
                else if (!isset($mdassign[$sqlma['daynumber']]['assignment2']))
                {
                    $mdassign[$sqlma['daynumber']]['assignment2']=$sqlma['assignment'];
                    $mdassign[$sqlma['daynumber']]['a2begintime']=substr($sqlma['begintime'], 0, 5);
                    $mdassign[$sqlma['daynumber']]['a2endtime']=substr($sqlma['endtime'], 0, 5);
                } //end elseif
                else
                {
                    $mdassign[$sqlma['daynumber']]['overflow'] = 1;
                } //end else
            } //end if

            else if ($sqlma['assigntype']==='3')
            {
                /*
                 * checking to see if the array has been changed from the original
                 * initialization to 0, $mdassign[$sqlma[0]][5]
                 * if not, and $sqlma[2] ($sqlma['assigntype']) is 3, a call1 
                 * assignment is contained and gets put in $mdassign[$sqlma[0]][5]
                 * and $mdassign[$sqlma[0]][6]
                 * if $mdassign[$sqlma[5]==0 is not true, then this
                 * is a call2 assignment and gets put in $mdassign[$sqlma[0]][8] and
                 * $mdassign[$sqlma[0]][9]
                 */
                if (!isset($mdassign[$sqlma['daynumber']]['call1']))
                {
                    $mdassign[$sqlma['daynumber']]['call1']=$sqlma['assignment'];
                    $mdassign[$sqlma['daynumber']]['c1begintime']=substr($sqlma['begintime'],0,5);
                    $mdassign[$sqlma['daynumber']]['c1endtime']=substr($sqlma['endtime'],0,5);
                } //end if
                else if (!isset($mdassign[$sqlma['daynumber']]['call2']))
                {
                    $mdassign[$sqlma['daynumber']]['call2']=$sqlma['assignment'];
                    $mdassign[$sqlma['daynumber']]['c2begintime']=substr($sqlma['begintime'],0,5);
                    $mdassign[$sqlma['daynumber']]['c2endtime']=substr($sqlma['endtime'],0,5);
                } //end else if
                else
                {
                    $mdassign[$sqlma['daynumber']]['overflow'] = 1;
                } //end else
            } //end else if

            else if ($sqlma['assigntype']==='4')
            {
                if(!isset($mdassign[$sqlma['daynumber']]['ah1']))
                {
                    $mdassign[$sqlma['daynumber']]['ah1']='Added Hours';
                    $mdassign[$sqlma['daynumber']]['ah1begintime']=substr($sqlma['begintime'],0,5);
                    $mdassign[$sqlma['daynumber']]['ah1endtime']=substr($sqlma['endtime'],0,5);
                } //end if
                else if (!isset($mdassign[$sqlma['daynumber']]['ah2']))
                {
                    $mdassign[$sqlma['daynumber']]['ah2']='Added Hours';
                    $mdassign[$sqlma['daynumber']]['ah2begintime']=substr($sqlma['begintime'],0,5);
                    $mdassign[$sqlma['daynumber']]['ah2endtime']=substr($sqlma['endtime'],0,5);
                } //end elseif
                else
                {
                    $mdassign[$sqlma['daynumber']]['overflow'] = 1;
                } //end else                
            } //end else if 
        } //end while ($sqlma = @mysql_fetch_array($sqlmq))
        
        for ($xx = 1; $xx<=$_SESSION['dimo']; $xx++)
        {
            if (isset($mdassign[$xx]['call1']) && 
               (trim($mdassign[$xx]['assignment1']) == 'None' || 
                trim($mdassign[$xx]['assignment1']) == 'Wkend' ||
                trim($mdassign[$xx]['assignment1']) == 'Hlday')
               )
            {
               $mdassign[$xx]['assignment1'] = null;
            }
        }
   } //end if (mysql_num_rows($sqlmq) > 0)  
     //END DESIGNATE: CURRENTMONTHARRAY
   else 
   {
      continue;    
   }
   
  
   /*
    * Setup loop to display the month calendar including all assignments for 
    * all anesthesiologists for the month 
    * DESIGNATE: MONTH_MD_DISPLAY
    */
   
    for ($x=1; $x<=$_SESSION['dimo']; $x++)
    {
      /*
       * If it is not a weekend or a holiday
       */
      if ($mdassign[$x]['weekend']==0 && $mdassign[$x]['overflow']==0)
      {
         echo '
               <td bgcolor="white" width="75" height="60" align="center">';
      }
      /*
       * If $overflow is true (1), then the background becomes red to 
       * indicate there is more than can be displayed.
       */
      else if ($mdassign[$x]['overflow'] === 1)
      {
         echo '
               <td bgcolor="red" width="75" height="60" align="center">';
      }
      /*
       * If it is a weekend or holiday
       */
      else
      {
         echo '
               <td bgcolor="yellow" width="75" height="60" align="center">';
      }

      /*
       * This <table> is the block which displays the day in this particular row
       * of this anesthesiologist in the larger table of the month.
       */
      echo '
                    <table cellspacing="0" cellpadding="0"
                            align="center" width="98%">
                        <tr>';


      /*
       * If the weekend is 0 (not a weekend, paint the background in white
       * Otherwise, paint it in yellow
       * Then, display assignment1.
       * If assignment1 is 'None' or 'Vac' or 'Hlday', etc., display only the
       * assignment, not the times.
       */
      if (isset($mdassign[$x]['assignment1']))
      {
        if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
            echo '
                                <td height="11px" bgcolor="white" width="98%" align="center"
                                    style="font-size:9px;">';
        else if ($mdassign[$x]['overflow'] === 1)
            echo '
                                <td height="11px" bgcolor="red" width="98%" align="center"
                                    style="font-size:9px;">';
        else
            echo '
                                <td height="11px" bgcolor="yellow" width="98%" align="center"
                                    style="font-size:9px;">';        
            echo
                $mdassign[$x]['assignment1'];
        
            echo'
                            </td>
                        </tr>
                        <tr>';
            
      }
    
    
         if  (   !isset ($mdassign[$x]['assignment1'])
                 || trim($mdassign[$x]['assignment1']) == "None" 
                 || trim($mdassign[$x]['assignment1']) == "UWVac"
                 || trim($mdassign[$x]['assignment1']) == "UwVac"
                 || trim($mdassign[$x]['assignment1']) == "Wkend"
                 || trim($mdassign[$x]['assignment1']) == "OrOff"
                 || trim($mdassign[$x]['assignment1']) == "OhOff"
                 || trim($mdassign[$x]['assignment1']) == "M Off"
                 || trim($mdassign[$x]['assignment1']) == "ObOff"
                 || trim($mdassign[$x]['assignment1']) == "Hlday"
                 || trim($mdassign[$x]['assignment1']) == "H Off"
                 || trim($mdassign[$x]['assignment1']) == "Off"
                 || trim($mdassign[$x]['assignment1']) == "Vac"
                 || trim($mdassign[$x]['assignment1']) == "Unwanted Vac"
                )
            {}
         else 
         {
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';

                        echo
                            '('.$mdassign[$x]['a1begintime'].'-'.
                                $mdassign[$x]['a1endtime'].')';
                    echo'
                                    </td>
                                </tr>
                                <tr>';
         }

         if (isset($mdassign[$x]['assignment2']))
         { 
           if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                echo '
                                    <td height="11px" bgcolor="white" width="98%" align="center"
                                        style="font-size:9px;">';
            else if ($mdassign[$x]['overflow'] === 1)
                echo '
                                    <td height="11px" bgcolor="red" width="98%" align="center"
                                        style="font-size:9px;">';
            else
                echo '
                                    <td height="11px" bgcolor="yellow" width="98%" align="center"
                                        style="font-size:9px;">';


                echo
                    $mdassign[$x]['assignment2'];

            echo'
                                </td>
                            </tr>
                            <tr>';
          }



          if  (   !isset($mdassign[$x]['assignment2']) 
                 || trim($mdassign[$x]['assignment2']) === "None" 
                 || trim($mdassign[$x]['assignment2']) === "UWVac"
                 || trim($mdassign[$x]['assignment1']) === "UwVac"
                 || trim($mdassign[$x]['assignment2']) === "Wkend"
                 || trim($mdassign[$x]['assignment2']) === "OrOff"
                 || trim($mdassign[$x]['assignment2']) === "OhOff"
                 || trim($mdassign[$x]['assignment2']) === "M Off"
                 || trim($mdassign[$x]['assignment2']) === "ObOff"
                 || trim($mdassign[$x]['assignment2']) === "Hlday"
                 || trim($mdassign[$x]['assignment2']) === "H Off"
                 || trim($mdassign[$x]['assignment2']) === "Off"
                 || trim($mdassign[$x]['assignment2']) === "Vac"
                )
            {}
            else
            {
              if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                        echo
                            '('.$mdassign[$x]['a2begintime'].'-'.
                                $mdassign[$x]['a2endtime'].')';

                echo'
                                    </td>
                                </tr>
                                <tr>';
            }



            if (isset($mdassign[$x]['call1']))
            {
               if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                          echo
                              $mdassign[$x]['call1'];

                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }
    
            if (isset($mdassign[$x]['call1']))
            {
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                    echo
                            '('.$mdassign[$x]['c1begintime'].'-'.
                                $mdassign[$x]['c1endtime'].')';

                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }



            if (isset($mdassign[$x]['call2']))
            {    

              if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                          echo
                              $mdassign[$x]['call2'];

                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }
    
            if (isset($mdassign[$x]['call2']))
            {
    
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                    echo
                            '('.$mdassign[$x]['c2begintime'].'-'.
                                $mdassign[$x]['c2endtime'].')';

                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }

            
            
            
            
            

            if (isset($mdassign[$x]['ah1']))
            {   
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                          echo
                              $mdassign[$x]['ah1'];

                    echo'
                                    </td>
                                </tr>
                                <tr>
                                ';
            }
            
            if (isset($mdassign[$x]['ah1']))
            {
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                    echo
                            '('.$mdassign[$x]['ah1begintime'].'-'.
                                $mdassign[$x]['ah1endtime'].')';

                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }
            
            if (isset($mdassign[$x]['ah2']))
            { 
            
                  if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';

                  else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                  else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                          echo
                              $mdassign[$x]['ah2'];
                    echo'
                                    </td>
                                </tr>
                                <tr>';
            }
            
             if (isset($mdassign[$x]['ah2']))
             {   
                if ($mdassign[$x]['weekend']==='0' && $mdassign[$x]['overflow'] === 0)
                    echo '
                                        <td height="11px" bgcolor="white" width="98%" align="center"
                                            style="font-size:9px;">';
                else if ($mdassign[$x]['overflow'] === 1)
                    echo '
                                        <td height="11px" bgcolor="red" width="98%" align="center"
                                            style="font-size:9px;">';
                else
                    echo '
                                        <td height="11px" bgcolor="yellow" width="98%" align="center"
                                            style="font-size:9px;">';


                    echo
                            '('.$mdassign[$x]['ah2begintime'].'-'.
                                $mdassign[$x]['ah2endtime'].')';
             }

                    echo'
                                    </td>
                                </tr>
                            </table>';
    } //end for ($x=1; $x<=$_SESSION['dimo']; $x++)
      //END DESIGNATE: MONTH_MD_DISPLAY
    
    
    
    
      echo '<td bgcolor="white" width="150" height="50" align="center">
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="20" bgcolor="white" width="148" align="center"
                            style="font-size:small;">
                            <b>';
      echo                      $sqlmdf[1];
      echo '                </b>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td height="20" bgcolor="white" width="148" align="center"
                            style="font-size:small;">';
      echo                      $sqlmdf[0];
      echo '            </td>
                    </tr>
                </table>
            </td>';

      $dateline=$dateline+1;
      echo '
          </tr>';     
   } //end while ($sqlmdf = mysql_fetch_array($sqlmd_number_last))
    //END DESIGNATE: MONTHDISPLAY
  
  echo '</table>';  
  echo '<br><br>'; 
  echo '<center><font color = "red">*** Note:  If a block background is red, there is too much
      information to adequately display results.  All details are contained in
      the individual partner\'s page for that day and in the primary database
      table.</center></font color><br><br>';
  include ('includes/footer.html'); 
} //end else
?>

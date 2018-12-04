<?php
/*
 * Version 02_02
 */
/*
 * LAST REVISED 2013-05-19
 */
/*
 * REVISION ON 2011-06-13 WAS TO UPDATE FOR THE NEW PAY RULES
 */
/*
 * Revision on 2011-08-05 to better format the code and to update to accurately display the
 * assignments with appropriate added hours and changes in the schedule.
 */
/*
 * Revised on 2011-08-23 so the day assignment will not display on the calendar if the partner
 * has a call assignment (as not to confuse users about what the assignment is).
 */
/*
 * Revised 2013-05-06 to initialize $day--AdjustHours variables, remove variables
 * which were not being used, etc.
 */

function daycalendar($numericaldayofmonth)
{
     $checkSameDay=0;
     //$checkSameDay is 0 if the day assignment and times are the same
     $checkSameCall=0;
     //$checkSameCall is 0 if the call assignment and times are the same

     
     $firstDayAssignment=false;
     $firstCallAssignment=false;
     $weekendCallPossible=0;
     $unwantedVac=0;

     $domo = $numericaldayofmonth+1;
     
     $callA2AdjustHours = 0;
     $callA1AdjustHours = 0;
     $dayA1AdjustHours = 0;
     $dayA2AdjustHours = 0;
     
     $sameCallAssignment=1;
     $sameDayAssignment=0;









     /*
      * Get the day assignments registered in the table "monthassignment"
      */
     $monthAssignDayStatement = 
                       "SELECT assignment, beginblock, endblock
                        FROM monthassignment
                        WHERE daynumber=$domo
                        AND yearnumber={$_SESSION['dty']}
                        AND monthnumber={$_SESSION['dtm']}
                        AND mdnumber={$_SESSION['schedmdnum']}
                        AND assigntype=1
                        ORDER BY beginblock, assignment";
     $monthAssignDayQuery = mysql_query($monthAssignDayStatement);
     //var_dump($domo);


     /*
      * Get the originally assigned day assignment from the table 
      * "originalmonthassignments".
      */
     $originalMonthAssignmentCheckStatement = 
                            "   SELECT assignment, beginblock, endblock
                                FROM originalmonthassignments
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND mdnumber={$_SESSION['schedmdnum']}
                                AND assigntype=1
                                ORDER BY beginblock, assignment";
     $originalMonthAssignmentCheckQuery = 
                            mysql_query($originalMonthAssignmentCheckStatement);
     
     /*
      * Compare the original assignment with what is now in the table 
      * "monthassignment".
      * If there are differences, set $checkSameDay=1.
      */
     if (
             mysql_num_rows($monthAssignDayQuery)
             ==
             1
        )
     {  
             $originalMonthAssignmentCheck =
                         @mysql_fetch_array($originalMonthAssignmentCheckQuery);
             $monthAssignDay = @mysql_fetch_array($monthAssignDayQuery);
             /*
             var_dump($originalMonthAssignmentCheck['assignment']);
             var_dump($monthAssignDay['assignment']);
             var_dump($originalMonthAssignmentCheck['beginblock']);
             var_dump($monthAssignDay['beginblock']);
             var_dump($originalMonthAssignmentCheck['endblock']);
             var_dump($monthAssignDay['endblock']);
              *
              */
             if (
                 trim($originalMonthAssignmentCheck['assignment'])!=
                     trim($monthAssignDay['assignment'])
                 ||
                 $originalMonthAssignmentCheck['beginblock']!=$monthAssignDay['beginblock']
                 ||
                 $originalMonthAssignmentCheck['endblock']!=$monthAssignDay['endblock']
                )
             {
                 $checkSameDay = 1;
             }
     }
     /*
      * If $monthAssignDayQuery has more than 1 row, then $checkSameDay cannot
      * be the same and gets assigned the value 1.
      */
     else
     {
         $checkSameDay = 1;
     }







/*
 * Repeat the above process for the call assignments.
 */
     $monthAssignCallStatement =
                       "SELECT assignment, beginblock, endblock
                        FROM monthassignment
                        WHERE daynumber=$domo
                        AND yearnumber={$_SESSION['dty']}
                        AND monthnumber={$_SESSION['dtm']}
                        AND mdnumber={$_SESSION['schedmdnum']}
                        AND assigntype=3
                        ORDER BY beginblock, assignment";
     $monthAssignCallQuery = mysql_query($monthAssignCallStatement);

     $originalMonthAssignmentCallCheckStatement =
                            "   SELECT assignment, beginblock, endblock
                                FROM originalmonthassignments
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND mdnumber={$_SESSION['schedmdnum']}
                                AND assigntype=3
                                ORDER BY beginblock, assignment";
     $originalMonthAssignmentCallCheckQuery = 
                        mysql_query($originalMonthAssignmentCallCheckStatement);


     if (
             mysql_num_rows($monthAssignCallQuery)
             ==
             1
        )
     {
////////////////////////////////////////////////////////////////////////////////
         /*
          * What is the purpose of $weekendCallPossible?
          */
////////////////////////////////////////////////////////////////////////////////
             $weekendCallPossible=1;
             $originalMonthAssignmentCallCheck =
                     @mysql_fetch_array($originalMonthAssignmentCallCheckQuery);
             $monthAssignCall = @mysql_fetch_array($monthAssignCallQuery);
             if (
                 $originalMonthAssignmentCallCheck['assignment']!=
                     $monthAssignCall['assignment']
                 ||
                 $originalMonthAssignmentCallCheck['beginblock']!=
                     $monthAssignCall['beginblock']
                 ||
                 $originalMonthAssignmentCallCheck['endblock']!=
                     $monthAssignCall['endblock']
                )
             {
                 $checkSameCall = 1;
             }
     }
     else if (mysql_num_rows($monthAssignCallQuery)==0 
              && 
              mysql_num_rows($originalMonthAssignmentCallCheckQuery)==0)
     {
     }
     else
     {
         $checkSameCall = 1;
         $weekendCallPossible=1;
     }

/*
 * At this point if the day assignments are not the same as the original assignments, then
 * $checkSameDay = 1
 * If the call assignments are not the same as the original assignments, then
 * $checkSameCall = 1
 */












/*
 * First, let's deal with the day assignment(s)
 *
 * If there is only one entry in the table "monthassignments" for the day assignment
 * display the assignment with the appropriate number of subtracted hours (if any).
 *
 * If the assignment is different from the original assignment change the background color and
 * the display table entry.
 *
 * If there is more than one entry in the table "monthassignments" for the day assignment
 * AND
 * the assignments are the same,
 * add the hours of the two entries and display them as one line
 * with the appropriate number of subtracted hours
 *
 * If there is more than one entry in the table "monthassignments" for the day assignment
 * AND
 * the assignments are different,
 * display the assignments on different lines
 * with the appropriate number of subtracted hours (if any)
 *
 */

    $monthAssignDayStatement = "SELECT assignment, beginblock, endblock, weekend
                                FROM monthassignment
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND assigntype=1
                                AND mdnumber={$_SESSION['schedmdnum']}
                                ORDER BY beginblock";
    $monthAssignDayQuery = mysql_query($monthAssignDayStatement);
    
    if ($checkSameDay==0)
    {
        $monthAssignDayResult = mysql_fetch_array($monthAssignDayQuery);
        $dayA1 = $monthAssignDayResult['assignment'];
    }
    else if ($checkSameDay==1)
    {
         while ($monthAssignDayResult = mysql_fetch_array($monthAssignDayQuery))
         {
             if (mysql_num_rows($monthAssignDayQuery)==1)
             {
                $dayA1 = $monthAssignDayResult['assignment'];
                if (trim($dayA1)=='Unwanted Vac')
                    $unwantedVac = 1;
                $dayA1BeginBlock = $monthAssignDayResult['beginblock'];
                $dayA1EndBlock = $monthAssignDayResult['endblock'];
                $normalAssignDayStatement = " SELECT beginblock, endblock
                                                FROM assignments
                                                WHERE 
                                                (assignment LIKE
                                                    '{$monthAssignDayResult['assignment']}'
                                                 OR
                                                 assignment LIKE
                                                    '{$monthAssignDayResult['assignment']} '
                                                 OR
                                                 assignment LIKE
                                                    '{$monthAssignDayResult['assignment']}  ')
                                                    
                                                AND weekend =
                                                    {$monthAssignDayResult['weekend']}
                                                AND (
                                                        type_number=1
                                                        OR
                                                        type_number=4
                                                        OR
                                                        type_number=5
                                                        OR
                                                        type_number=7
                                                    )";
                $normalAssignDayQuery = mysql_query($normalAssignDayStatement);
                $normalAssignDayResult = mysql_fetch_array($normalAssignDayQuery);


                $normalBeginBlock = $normalAssignDayResult['beginblock'];
                $normalEndBlock = $normalAssignDayResult['endblock'];

                $dayA1AdjustHours += (
                                       (
                                           $dayA1EndBlock
                                           -
                                           $dayA1BeginBlock
                                       )
                                       -
                                       (
                                           $normalEndBlock
                                           -
                                           $normalBeginBlock
                                       )
                                     )/4;
                $sameDayAssignment=1;
             }
             else if ($firstDayAssignment)
             {
                 if (trim($monthAssignDayResult['assignment'])==trim($dayA1))
                 {
                     $normalAssignDayStatement = " SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '{$monthAssignDayResult['assignment']}%'
                                                    AND weekend =
                                                        {$monthAssignDayResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignDayQuery = mysql_query($normalAssignDayStatement);
                    $normalAssignDayResult = mysql_fetch_array($normalAssignDayQuery);


                    $normalBeginBlock = $normalAssignDayResult['beginblock'];
                    $normalEndBlock = $normalAssignDayResult['endblock'];


                    $dayA1AdjustHours += (
                                           (
                                               $dayA1EndBlock
                                               -
                                               $dayA1BeginBlock
                                           )
                                           +
                                           (
                                                $monthAssignDayResult['endblock']
                                                -
                                                $monthAssignDayResult['beginblock']
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $sameDayAssignment=1;
                 }
                 else
                 {
                    $normalAssignDayStatement = "  SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '{$monthAssignDayResult['assignment']}%'
                                                    AND weekend =
                                                        {$monthAssignDayResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignDayQuery = mysql_query($normalAssignDayStatement);
                    $normalAssignDayResult = mysql_fetch_array($normalAssignDayQuery);


                    $normalBeginBlock = $normalAssignDayResult['beginblock'];
                    $normalEndBlock = $normalAssignDayResult['endblock'];


                    $dayA2 = $monthAssignDayResult['assignment'];

                    $dayA2AdjustHours += (
                                           (
                                               $monthAssignDayResult['endblock']
                                               -
                                               $monthAssignDayResult['beginblock']
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $normalAssignDayStatement = "  SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '$dayA1%'
                                                    AND weekend =
                                                        {$monthAssignDayResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignDayQuery = mysql_query($normalAssignDayStatement);
                    $normalAssignDayResult = mysql_fetch_array($normalAssignDayQuery);

                    $dayA1AdjustHours += (
                                           (
                                               $dayA1EndBlock
                                               -
                                               $dayA1BeginBlock
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $sameDayAssignment=0;
                 }
             }
             else
             {
                $dayA1 = $monthAssignDayResult['assignment'];
                $dayA1BeginBlock = $monthAssignDayResult['beginblock'];
                $dayA1EndBlock = $monthAssignDayResult['endblock'];
                $firstDayAssignment=true;
             }
        }
    }


/*
 * Dealing with the call assignments
 */
     $monthAssignCallStatement = "SELECT assignment, beginblock, endblock, weekend
                                FROM monthassignment
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND assigntype=3
                                AND mdnumber={$_SESSION['schedmdnum']}
                                ORDER BY beginblock";
    $monthAssignCallQuery = mysql_query($monthAssignCallStatement);



    if ($checkSameCall==0)
    {
        $monthAssignCallResult = mysql_fetch_array($monthAssignCallQuery);
        $callA1 = $monthAssignCallResult['assignment'];
    }
    else if ($checkSameCall==1)
    {
         while ($monthAssignCallResult = mysql_fetch_array($monthAssignCallQuery))
         {
             if (mysql_num_rows($monthAssignCallQuery)==1)
             {
                $callA1 = $monthAssignCallResult['assignment'];
                $callA1BeginBlock = $monthAssignCallResult['beginblock'];
                $callA1EndBlock = $monthAssignCallResult['endblock'];
                $normalAssignCallStatement = " SELECT beginblock, endblock
                                                FROM assignments
                                                WHERE assignment LIKE
                                                    '{$monthAssignCallResult['assignment']}%'
                                                AND weekend =
                                                    {$monthAssignCallResult['weekend']}
                                                AND (
                                                        type_number=2
                                                        OR
                                                        type_number=4
                                                        OR
                                                        type_number=6
                                                    )";
                $normalAssignCallQuery = mysql_query($normalAssignCallStatement);
                $normalAssignCallResult = mysql_fetch_array($normalAssignCallQuery);


                $normalBeginBlock = $normalAssignCallResult['beginblock'];
                $normalEndBlock = $normalAssignCallResult['endblock'];


                $callA1AdjustHours += (
                                       (
                                           $callA1EndBlock
                                           -
                                           $callA1BeginBlock
                                       )
                                       -
                                       (
                                           $normalEndBlock
                                           -
                                           $normalBeginBlock
                                       )
                                     )/4;
                $sameCallAssignment=1;
             }
             else if ($firstCallAssignment)
             {
                 if (trim($monthAssignCallResult['assignment'])==trim($callA1))
                 {
                     $normalAssignCallStatement = " SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '{$monthAssignCallResult['assignment']}%'
                                                    AND weekend =
                                                        {$monthAssignCallResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignCallQuery = mysql_query($normalAssignCallStatement);
                    $normalAssignCallResult = mysql_fetch_array($normalAssignCallQuery);


                    $normalBeginBlock = $normalAssignCallResult['beginblock'];
                    $normalEndBlock = $normalAssignCallResult['endblock'];


                    $callA1AdjustHours += (
                                           (
                                               $callA1EndBlock
                                               -
                                               $callA1BeginBlock
                                           )
                                           +
                                           (
                                                $monthAssignCallResult['endblock']
                                                -
                                                $monthAssignCallResult['beginblock']
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $sameCallAssignment=1;
                 }
                 else
                 {
                    $normalAssignCallStatement = "  SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '{$monthAssignCallResult['assignment']}%'
                                                    AND weekend =
                                                        {$monthAssignCallResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignCallQuery = mysql_query($normalAssignCallStatement);
                    $normalAssignCallResult = mysql_fetch_array($normalAssignCallQuery);


                    $normalBeginBlock = $normalAssignDayResult['beginblock'];
                    $normalEndBlock = $normalAssignDayResult['endblock'];


                    $callA2 = $monthAssignCallResult['assignment'];

                    $callA2AdjustHours += (
                                           (
                                               $monthAssignCallResult['endblock']
                                               -
                                               $monthAssignCallResult['beginblock']
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $normalAssignCallStatement = "  SELECT beginblock, endblock
                                                    FROM assignments
                                                    WHERE assignment LIKE
                                                        '$callA1%'
                                                    AND weekend =
                                                        {$monthAssignCallResult['weekend']}
                                                    AND (
                                                            type_number=2
                                                            OR
                                                            type_number=4
                                                            OR
                                                            type_number=6
                                                        )";
                    $normalAssignCallQuery = mysql_query($normalAssignCallStatement);
                    $normalAssignCallResult = mysql_fetch_array($normalAssignCallQuery);

                    $callA1AdjustHours += (
                                           (
                                               $callA1EndBlock
                                               -
                                               $callA1BeginBlock
                                           )
                                           -
                                           (
                                               $normalEndBlock
                                               -
                                               $normalBeginBlock
                                           )
                                         )/4;
                    $sameCallAssignment=0;
                 }
             }
             else
             {
                $callA1 = $monthAssignCallResult['assignment'];
                $callA1BeginBlock = $monthAssignCallResult['beginblock'];
                $callA1EndBlock = $monthAssignCallResult['endblock'];
                $firstCallAssignment=true;
             }
         }
    }











	 echo'
            <td bgcolor="#000000" width="14%" height="150px" align="center"
            onMouseOver="bgColor=\'yellow\'" onMouseOut="bgColor=\'black\'"
            onClick="window.location.href=\'day_display.php?dai='.($numericaldayofmonth+1).'\'">
    <table border="0" cellspacing="0" cellpadding="0" style="color:black" width="98%">
        <tr>
            <td bgcolor="#ffffff" height="20" width="100%" align="left" style="font-size:small">
                '.++$numericaldayofmonth.'
            </td>
        </tr>
	<tr>
            <td bgcolor="#ffffff" height="10" width="100%" align="center">
            </td>
        </tr>
        <tr>';

/*
 * By this point we have these variables which apply here.
 *
 * $dayA1
 * $dayA1AdjustHours
 *
 * $dayA2
 * $dayA2AdjustHours
 *
 * $callA1
 * $callA1AdjustHours
 *
 * $callA2
 * $callA2AdjustHours
 */
        if ($weekendCallPossible==1 && $monthAssignDayResult['weekend']==1)
        {
            if (isset($callA1))
            {
                echo '
                    <td bgcolor="GREEN" height="20" width="100%" align="center">
                    </td>
                </tr>';
            }
            else
            {
                echo '
                    <td style="color:green; font-style:italic;" border="1" bgcolor="antiquewhite"
                        height="20" width="100%" align="center"><b>
                    Wkend
                    </td>
                </tr>';
            }
            echo'
                <tr>
                    <td bgcolor="GREEN" height="20" width="100%" align="center">
                    </td>
                </tr>';
        }

        else if ($checkSameDay==0)
        {
           echo'
                    <td style="color:white" bgcolor="GREEN" height="20" width="100%"
                        align="center"><b>';
                if (!empty($dayA1))
                {
                echo
                   $dayA1
                   ;
                }
                else
                {
                    echo
                    'None'
                   ;
                }
             echo'
                 </td>
            </tr>
            <tr>
                <td bgcolor="GREEN" height="20" width="100%" align="center">
		</td>
            </tr>';
        }
           
        else if ($checkSameDay==1)
        {
           if ($sameDayAssignment==1)
           {
               echo'
                <td style="color:green; font-style:italic;" border="1" bgcolor="antiquewhite"
                 height="20" width="100%" align="center"><b>';
               if ($dayA1AdjustHours==0   || $unwantedVac==1)
               {
                   if (!empty($dayA1))
                   {
                   echo
                      $dayA1
                      ;
                   }
                   else
                   {
                       echo
                       'None'
                      ;
                   }
                   echo'
                       </b>
                       </td>';
               }
               else
               {
                   if (!empty($dayA1))
                   {
                   echo
                      $dayA1.
                           ' ('.number_format($dayA1AdjustHours,2,'.',',').')'
                      ;
                   }
                   else
                   {
                       echo
                       'None'
                      ;
                   }
                   echo'
                      </b>
                      </td>';
               }
               echo ' 
                   </tr>
                   <tr>
                      <td bgcolor="GREEN" height="20px" width="100%" align="center"
                      </td>
                   </tr>';
           }
           else if ($sameDayAssignment==0)
           {
               echo'
                <td style="color:green; font-style:italic;" border="1" bgcolor="antiquewhite"
                 height="20" width="100%" align="center"><b>';
               if ($dayA1AdjustHours==0)
               {
                   if (!empty($dayA1))
                   {
                   echo
                      $dayA1
                      ;
                   }
                   else
                   {
                       echo
                       'None'
                      ;
                   }
                   echo'
                      </b>
                      </td>
                      </tr>
                      <tr>';
               }
               else
               {
                   if (!empty($dayA1))
                   {
                   echo
                      $dayA1.
                           ' ('.number_format($dayA1AdjustHours,2,'.',',').')'
                      ;
                   }
                   else
                   {
                       echo
                       'None'
                      ;
                   }
                   echo'
                      </b>
                      </td>
                     </tr>
                     <tr>';
               }
               echo'
                <td style="color:green; font-style:italic;" border="1" bgcolor="antiquewhite"
                 height="20" width="100%" align="center"><b>';
               if ($dayA2AdjustHours==0)
               {
                   if (!empty($dayA2))
                   {
                   echo
                      $dayA2
                      ;
                   }
                   echo'
                      </b>
                      </td>';
               }
               else
               {
                   if (!empty($dayA2))
                   {
                   echo
                      $dayA2.
                           ' ('.number_format($dayA2AdjustHours,2,'.',',').')'
                      ;
                   }
                   echo'
                      </b>
                      </td>';
               }
           }
        }






        
        if ($checkSameCall==0)
        {
           echo'
                </tr>
                <tr>
                    <td style="color:BLACK" bgcolor="YELLOW" height="20" width="100%"
                        align="center"><b>';
                if (!empty($callA1))
                {
                echo
                   $callA1
                   ;
                }
             echo'
            </tr>
            <tr>
                <td bgcolor="YELLOW" height="20" width="100%" align="center">
		</td>
            </tr>';
        }

        else if ($checkSameCall==1)
        {
           if ($sameCallAssignment==1)
           {
               echo'
             </tr>
             <tr>
                <td style="color:YELLOW; font-style:italic;" border="1" bgcolor="BLACK"
                 height="20" width="100%" align="center"><b>';
               if ($callA1AdjustHours==0)
               {
                   if (!empty($callA1))
                   {
                   echo
                      $callA1
                      ;
                   }
               }
               else
               {
                   if (!empty($callA1))
                   {
                   echo
                      $callA1.
                           ' ('.number_format($callA1AdjustHours,2,'.',',').')'
                      ;
                   }
                   echo'
                      </b>
                      </td>';
               }
               echo '
                   </tr>
                   <tr>
                      <td bgcolor="YELLOW" height="20px" width="100%" align="center"
                      </td>
                   </tr>';
           }
           else if ($sameCallAssignment==0)
           {
               echo'
              </tr>
              <tr>
                <td style="color:YELLOW; font-style:italic;" border="1" bgcolor="BLACK"
                 height="20" width="100%" align="center"><b>';
               if ($callA1AdjustHours==0)
               {
                   if (!empty($callA1))
                   {
                   echo
                      $callA1
                      ;
                   }
               }
               else
               {
                   if (!empty($callA1))
                   {
                   echo
                      $callA1.
                           ' ('.number_format($callA1AdjustHours,2,'.',',').')'
                      ;
                   }
               }
                   echo'
                      </b>
                      </td>
                     </tr>
                     <tr>';
               
               echo'
                <td style="color:YELLOW; font-style:italic;" border="1" bgcolor="BLACK"
                 height="20" width="100%" align="center"><b>';
               if ($callA2AdjustHours==0)
               {
                   if (!empty($callA2))
                   {
                   echo
                      $callA2
                      ;
                   }
               }
               else
               {
                   if (!empty($callA2))
                   {
                   echo
                      $callA2.
                           ' ('.number_format($callA2AdjustHours,2,'.',',').')'
                      ;
                   }
               }
                   echo'
                      </b>
                      </td>';
               
           }
        }

        $addedHours=0;
        $addedHoursStatement = "SELECT beginblock, endblock
                                FROM monthassignment
                                WHERE assigntype=4
                                AND daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND mdnumber={$_SESSION['schedmdnum']}
                                ORDER BY beginblock";
        $addedHoursQuery = mysql_query($addedHoursStatement);
        while ($addedHoursResult = mysql_fetch_array($addedHoursQuery))
        {
            $addedHours += ($addedHoursResult['endblock']-$addedHoursResult['beginblock'])/4;
        }

            if ($addedHours==0)
            {
                echo'
            </tr>
            <tr>
                <td bgcolor="#e5e5e5" height="20" width="100%" align="center">
		</td>
            </tr>
		  ';
            }
            else
            {
                echo'
            </tr>
            <tr>
                <td bgcolor="#e5e5e5" height="20" width="100%" align="center">
                    Added Hours ('.number_format($addedHours,2,'.',',').')
		</td>
            </tr>
		  ';
            }

            echo'
            <tr>
                <td bgcolor="#ffffff" height="16" width="100%" align="center">
                </td>
            </tr>
        </table>
    </td>';
} 
?>
<?php

function checkDay($domo)
{   
    $checkSameDay=0;
    //$checkSameDay is 0 if the day assignment and times are the same
    /*
      * Code immediately below goes a long way to simply assign the correct
      * value to $checkSameDay (0 if the assignment and the original assignment
      * are the same, 1 if they are not).
      * FOR DAY ASSIGNMENTS
      */

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


     /*
      * Get the originally assigned day assignment from the table "originalmonthassignments"
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
     $originalMonthAssignmentCheckQuery = mysql_query($originalMonthAssignmentCheckStatement);
     
     /*
      * Compare the original assignment with what is now in the table "monthassignment"
      * If there are differences, set $checkSameDay=1
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
     else
     {
         $checkSameDay = 1;
     }
     return $checkSameDay;
}
?>

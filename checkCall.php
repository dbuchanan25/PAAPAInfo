<?php

function checkCall($domo)
{ 
    $checkSameCall = 0;

/*
      * Code immediately below goes a long way to simply assign the correct
      * value to $checkSameCall (0 if the assignment and the original assignment
      * are the same, 1 if they are not).
      * FOR CALL ASSIGNMENTS
      */

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

     $originalMonthAssignmentCheckStatement =
                            "   SELECT assignment, beginblock, endblock
                                FROM originalmonthassignments
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND mdnumber={$_SESSION['schedmdnum']}
                                AND assigntype=3
                                ORDER BY beginblock, assignment";
     $originalMonthAssignmentCheckQuery = mysql_query($originalMonthAssignmentCheckStatement);


     if (
             mysql_num_rows($monthAssignCallQuery)
             ==
             1
        )
     {
             $weekendCallPossible=1;
             $originalMonthAssignmentCheck =
                                @mysql_fetch_array($originalMonthAssignmentCheckQuery);
             $monthAssignCall = @mysql_fetch_array($monthAssignCallQuery);
             if (
                 $originalMonthAssignmentCheck['assignment']!=$monthAssignCall['assignment']
                 ||
                 $originalMonthAssignmentCheck['beginblock']!=$monthAssignCall['beginblock']
                 ||
                 $originalMonthAssignmentCheck['endblock']!=$monthAssignCall['endblock']
                )
             {
                 $checkSameCall = 1;
             }
     }
     else if (mysql_num_rows($monthAssignCallQuery)==0 
              && 
              mysql_num_rows($originalMonthAssignmentCheckQuery)==0)
     {
     }
     else
     {
         $checkSameCall = 1;
         
         //////////////////////////////////////////////////////////////////////
         //PURPOSE OF $weekendCallPossible?????????????????????????????????????
         $weekendCallPossible=1;
         //////////////////////////////////////////////////////////////////////
     }
     return $checkSameCall;
}
?>

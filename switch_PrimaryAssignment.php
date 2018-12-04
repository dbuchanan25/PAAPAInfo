<?php
/*
 * Last Revised: 2014-01-17
 */
/*
 * Revised 2014-01-17 to record insert statements into log.txt
 */
function switch_PrimaryAssignment()
{
     /*
     * Checking to see if the change is C OH or H 1.  If it is then there must be caution
     * to make sure the call part of the assignment is dealt with correctly.
     */
            if (trim($_SESSION['sqlassfetch'])=='C OH')
            {
     	?>
                <script type="text/javascript">
                alert("You are trading Orthopedic Call Assignment WEEKDAY ONLY.  If you are\n"+
                      "also trading the CALL portion of this assignment, please\n"+
                      "make sure the CALL portion also gets changed.");
                </script>
        <?php
            }
            if (trim($_SESSION['sqlassfetch'])=='H 1')
            {
     	?>
                <script type="text/javascript">
                alert("You are trading Heart Call Assignment WEEKDAY ONLY.  If you are also\n"+
                      "trading the CALL portion of this assignment, please\n"+
                      "make sure the CALL portion also gets changed.");
                </script>
        <?php
            }

            /*
             * Select all instances (should only be one) where the assignment being switched is
             * entered for the specified date.
             */
            $sqlsame = "SELECT *
                        FROM monthassignment
                        WHERE monthnumber={$_SESSION['dtm']}
                        AND daynumber={$_SESSION['dai']}
                        AND yearnumber={$_SESSION['dty']}
                        AND assignment='{$_SESSION['sqlassfetch']}'
                        AND assigntype=1";
            $sqlsamequery = mysql_query($sqlsame);
            $sqlsamefetch = @mysql_fetch_row($sqlsamequery);


            /*
             * Select the assignment for the person making the switch.
             */
            $sqloriginal = "SELECT *
                            FROM monthassignment
                            WHERE monthnumber={$_SESSION['dtm']}
                            AND daynumber={$_SESSION['dai']}
                            AND yearnumber={$_SESSION['dty']}
                            AND assigntype=1
                            AND mdnumber={$_SESSION['schedmdnum']}";
            $sqloriginalquery = mysql_query($sqloriginal);
            $sqloriginalfetch = @mysql_fetch_row($sqloriginalquery);


            /*
             * In addition to the assignment being switched to ($_SESSION['sqlassfetch']), if the
             * assignment being switched out by the user is C OH or H 1 there is a warning to
             * try and make sure the call part of the assignment is not forgotten.
             */
            if (trim($sqloriginalfetch[4])=='C OH')
	    {
     	?>
        <script type="text/javascript">
        alert("You are trading Orthopedic Call Assignment.  If you are also\n"+
              "trading the evening portion of this assignment, please\n"+
               "make sure the evening portion also gets changed.");
        </script>
        <?php
	    }

            if (trim($sqloriginalfetch[4])=='H 1')
            {
     	?>
        <script type="text/javascript">
        alert("You are trading Heart Call Assignment.  If you are also\n"+
              "trading the home call portion of this assignment, please\n"+
              "make sure the home call portion also gets changed.");
        </script>
        <?php
            }

            /*
             * The original assignment (the assignment being switched out) is entered into the
             * bumonthassignment table.
             */
            $sqlchange1 = "INSERT INTO bumonthassignment
                           VALUES ( $sqloriginalfetch[0],
                                    $sqloriginalfetch[1],
                                    $sqloriginalfetch[2],
                                    $sqloriginalfetch[3],
                                   '$sqloriginalfetch[4]',
                                    $sqloriginalfetch[5],
                                   '$sqloriginalfetch[6]',
                                    $sqloriginalfetch[7],
                                   '$sqloriginalfetch[8]',
                                    $sqloriginalfetch[9],
                                    $sqloriginalfetch[10],
                                    now(),
                                   '{$_SESSION['initials']}',
                                    NULL)";
     
    $istring = str_replace("'","",$sqlchange1);                              
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                               
                                   
            $sqlchange1query = mysql_query($sqlchange1);

            /*
             * The assignment being switched into is also placed into the bumonthassignment
             * table.
             */
            $sqlchange2 = "INSERT INTO bumonthassignment
                           VALUES ( $sqlsamefetch[0],
                                    $sqlsamefetch[1],
                                    $sqlsamefetch[2],
                                    $sqlsamefetch[3],
                                   '$sqlsamefetch[4]',
                                    $sqlsamefetch[5],
                                   '$sqlsamefetch[6]',
                                    $sqlsamefetch[7],
                                   '$sqlsamefetch[8]',
                                    $sqlsamefetch[9],
                                    $sqlsamefetch[10],
                                    now(),
                                   '{$_SESSION['initials']}',
                                    NULL)";
          
    $istring = str_replace("'","",$sqlchange2);                               
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                               
                                   
            mysql_query($sqlchange2);

            /*
             * Delete from the monthassignment table the original assignments.
             */
            $sqldelete1 = "DELETE FROM monthassignment
                           WHERE counter=$sqloriginalfetch[13]";
            mysql_query($sqldelete1);

            $sqldelete1 = "DELETE FROM monthassignment
                           WHERE counter=$sqlsamefetch[13]";
            mysql_query($sqldelete1);

            /*
             * Insert into the monthassignment table the switched assignments.
             * DOES ITEM 5, THE ASSIGNMENT TYPE NEED TO BE SWITCHED???????????????????????????????
             */
            $sqlinsert1 = "INSERT INTO monthassignment".
                          " VALUES ( $sqlsamefetch[0],".
                                    "$sqloriginalfetch[1],".
                                    "$sqloriginalfetch[2],".
                                    "$sqloriginalfetch[3],".
                                   "'$sqloriginalfetch[4]',".
                                    "1,".
                                   "'$sqloriginalfetch[6]',".
                                    "$sqloriginalfetch[7],".
                                   "'$sqloriginalfetch[8]',".
                                    "$sqloriginalfetch[9],".
                                    "$sqloriginalfetch[10],".
                                    "now(),".
                                   "'{$_SESSION['initials']}',".
                                    "NULL)";
                                   
    $istring = str_replace("'","",$sqlinsert1);                                
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                               
                                   
            $file = fopen("log.txt", "a") or exit("Unable to open file!");
            $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
            $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                            "User: {$_SESSION['initials']}\n". 
                            "Page: switch_PrimaryAssignment.php\n".
                            "Statement: {$sqlinsert1}\n\n";
            fwrite($file, $logStatement);
            fclose($file);                       
                                   
            mysql_query($sqlinsert1);

	    $sqlinsert2 = "INSERT INTO monthassignment".
		          " VALUES ( $sqloriginalfetch[0],".
                                    "$sqlsamefetch[1],".
                                    "$sqlsamefetch[2],".
                                    "$sqlsamefetch[3],".
                                   "'$sqlsamefetch[4]',".
                                    "1,".
                                   "'$sqlsamefetch[6]',".
                                    "$sqlsamefetch[7],".
                                   "'$sqlsamefetch[8]',".
                                    "$sqlsamefetch[9],".
                                    "$sqlsamefetch[10],".
                                    "now(),".
                                   "'{$_SESSION['initials']}',".
                                    "NULL)";
                                   
    $istring = str_replace("'","",$sqlinsert2);                                
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                               
                                   
            $file = fopen("log.txt", "a") or exit("Unable to open file!");
            $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
            $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                            "User: {$_SESSION['initials']}\n". 
                            "Page: switch_PrimaryAssignment.php\n".
                            "Statement: {$sqlinsert2}\n\n";
            fwrite($file, $logStatement);
            fclose($file);                       
                                   
	    mysql_query($sqlinsert2);




/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//This updates the MONTHCAL table to reflect the changes in the partner swap.                   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
		$sqlmcdelete1 = "DELETE FROM monthcal
		                 WHERE monthnumber={$_SESSION['dtm']}
                                 AND daynumber={$_SESSION['dai']}
                                 AND yearnumber={$_SESSION['dty']}
                                 AND assignment='{$_SESSION['sqlassfetch']}'
                                 AND assigntype=1";
		mysql_query($sqlmcdelete1);

		$sqlmcdelete2 = "DELETE FROM monthcal
		                 WHERE monthnumber={$_SESSION['dtm']}
				 AND daynumber={$_SESSION['dai']}
                                 AND yearnumber={$_SESSION['dty']}
                                 AND assigntype=1
                                 AND mdnumber={$_SESSION['schedmdnum']}";
		mysql_query($sqlmcdelete2);


/*
 * Get the usual timeperiods from the assignments table to be able to calculate
 * if there are any deviations from the times of the assignments being switched.
 * If so, these are reflected in the monthcalendar (table monthcal) to be
 * displayed as subtracted or added hours for both partners.
 */
		$sqlhrs1 = "SELECT beginblock, endblock
		            FROM assignments
                            WHERE assignment='$sqloriginalfetch[4]'
		            AND weekend=$sqloriginalfetch[10]";
		$sqlhrs1q = mysql_query($sqlhrs1);
		$sqlhrs1a = @mysql_fetch_row($sqlhrs1q);

		$sqlhrs2 = "SELECT beginblock, endblock
		            FROM assignments
                            WHERE assignment='$sqlsamefetch[4]'
		            AND weekend=$sqlsamefetch[10]";
		$sqlhrs2q = mysql_query($sqlhrs2);
		$sqlhrs2a = @mysql_fetch_row($sqlhrs2q);


		$sqlinsert1 = "INSERT INTO monthcal
		               VALUES ( $sqlsamefetch[0],
                                        $sqloriginalfetch[1],
                                        $sqloriginalfetch[2],
					$sqloriginalfetch[3],
                                       '$sqloriginalfetch[4]',
                                        1,
					$sqloriginalfetch[10],
                                        '$sqloriginalfetch[6]',
                                        '$sqloriginalfetch[8]')";
                $sqlinsert1query = mysql_query($sqlinsert1);
                
    $istring = str_replace("'","",$sqlinsert1);             
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);            


		$sqlinsert2 = "INSERT INTO monthcal
		               VALUES ( $sqloriginalfetch[0],
                                        $sqlsamefetch[1],
                                        $sqlsamefetch[2],
					$sqlsamefetch[3],
                                       '$sqlsamefetch[4]',
                                        1,
                                        $sqlsamefetch[10],
					'$sqlsamefetch[6]',
                                        '$sqlsamefetch[8]')";
                
    $istring = str_replace("'","",$sqlinsert2);             
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_PrimaryAssignment.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);            
                
                mysql_query($sqlinsert2);


                 echo ' <h2 align="center">The switch has been made.</h2>
                        <br>
                        <h3 align="center">To continue click on Submit.</h3>
                        <br>
		      ';
}
?>

<?php
/*
 * Last Revised: 2014-01-17
 */
/*
 * Revised 2014-01-17 to log changes in log.txt
 */
    function switch_HomeCall()
    {
            $sqlsame = "SELECT *
                        FROM monthassignment
		        WHERE monthnumber={$_SESSION['dtm']}
			AND daynumber={$_SESSION['dai']}
			AND yearnumber={$_SESSION['dty']}
			AND assignment='{$_SESSION['sqlassfetch']}'
                        AND assigntype=3";
	    $sqlsamequery = mysql_query($sqlsame);
	    $sqlsamefetch = @mysql_fetch_row($sqlsamequery);

	    $sqloriginal = "SELECT *
                            FROM monthassignment
		            WHERE monthnumber={$_SESSION['dtm']}
                            AND daynumber={$_SESSION['dai']}
                            AND yearnumber={$_SESSION['dty']}
                            AND assigntype=3
                            AND mdnumber={$_SESSION['schedmdnum']}
                            ORDER BY beginblock";
	    $sqloriginalquery = mysql_query($sqloriginal);
	    $sqloriginalfetch = @mysql_fetch_row($sqloriginalquery);

/*
 * $sqloriginalfetch array consists of:
 * 0.  mdnumber
 * 1.  monthnumber
 * 2.  daynumber
 * 3.  yearnumber
 * 4.  assignment
 * 5.  assigntype
 * 6.  bt
 * 7.  beginblock
 * 8.  et
 * 9.  endblock
 * 10.  weekend
 * 11.  entrytime
 * 12.  logmd
 * 13.  counter
 * FOR THE ASSIGNMENT BEING SWITCHED OUT
 */

/*
 * $sqlsamefetch array consists of:
 * 0.  mdnumber
 * 1.  monthnumber
 * 2.  daynumber
 * 3.  yearnumber
 * 4.  assignment
 * 5.  assigntype
 * 6.  bt
 * 7.  beginblock
 * 8.  et
 * 9.  endblock
 * 10.  weekend
 * 11.  entrytime
 * 12.  logmd
 * 13.  counter
 * FOR THE ASSIGNMENT BEING SWITCHED INTO
 */
	    if (!empty($sqloriginalfetch) && !empty($sqlsamefetch))
	    {
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
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);    
    
    
                $sqlchange1query = mysql_query($sqlchange1);

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
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
    
    
                $sqlchange1query = mysql_query($sqlchange2);

                $sqldelete1 = "DELETE FROM monthassignment
		               WHERE counter=$sqloriginalfetch[13]";
                $sqldelete1query = mysql_query($sqldelete1);

                $sqldelete1 = "DELETE FROM monthassignment
		               WHERE counter=$sqlsamefetch[13]";
                $sqldelete1query = mysql_query($sqldelete1);

/*
 * $sqloriginalfetch array consists of:
 * 0.  mdnumber
 * 1.  monthnumber
 * 2.  daynumber
 * 3.  yearnumber
 * 4.  assignment
 * 5.  assigntype
 * 6.  bt
 * 7.  beginblock
 * 8.  et
 * 9.  endblock
 * 10.  weekend
 * 11.  entrytime
 * 12.  logmd
 * 13.  counter
 * FOR THE ASSIGNMENT BEING SWITCHED OUT
 */

/*
 * $sqlsamefetch array consists of:
 * 0.  mdnumber
 * 1.  monthnumber
 * 2.  daynumber
 * 3.  yearnumber
 * 4.  assignment
 * 5.  assigntype
 * 6.  bt
 * 7.  beginblock
 * 8.  et
 * 9.  endblock
 * 10.  weekend
 * 11.  entrytime
 * 12.  logmd
 * 13.  counter
 * FOR THE ASSIGNMENT BEING SWITCHED INTO
 */

/*
* So here the 0 value (mdnumber) is being switched and the 5 value (assigntype)
* is being switched.
* DOES 5 NEED TO BE SWITCHED?????????????????????????????????????????????????????
*/
                $sqlinsert1 = "INSERT INTO monthassignment".
		              " VALUES ( $sqlsamefetch[0],".
                                        "$sqloriginalfetch[1],".
                                        "$sqloriginalfetch[2],".
                                        "$sqloriginalfetch[3],".
                                        "'$sqloriginalfetch[4]',".
					"3,".
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
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                                    
                                       
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: switch_HomeCall.php\n".
                                "Statement: {$sqlinsert1}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                       
                                       
                $sqlinsert1query = mysql_query($sqlinsert1);

                $sqlinsert2 = "INSERT INTO monthassignment".
		              " VALUES ( $sqloriginalfetch[0],".
                                        "$sqlsamefetch[1],".
                                        "$sqlsamefetch[2],".
					"$sqlsamefetch[3],".
                                        "'$sqlsamefetch[4]',".
                                        "3,".
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
    VALUES ({$_SESSION['initials']},{$_SESSION['schedmd']}, 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                                    
                                       
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: switch_HomeCall.php\n".
                                "Statement: {$sqlinsert2}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                       
                                       
                $sqlinsert2query = mysql_query($sqlinsert2);

//////////////////////////////////////////////////////////////////////////////////////////////////
//This updates the MONTHCAL table to reflect the changes in the partner swap.                   //
//////////////////////////////////////////////////////////////////////////////////////////////////
		$sqlmcdelete1 = "DELETE FROM monthcal
		                 WHERE monthnumber={$_SESSION['dtm']}
				 AND daynumber={$_SESSION['dai']}
                                 AND yearnumber={$_SESSION['dty']}
                                 AND assignment='{$_SESSION['sqlassfetch']}'
                                 AND assigntype=3";
		$sqlmcdelete1q = mysql_query($sqlmcdelete1);

		$sqlmcdelete2 = "DELETE FROM monthcal
		                 WHERE monthnumber={$_SESSION['dtm']}
				 AND daynumber={$_SESSION['dai']}
                                 AND yearnumber={$_SESSION['dty']}
                                 AND assigntype=3
                                 AND mdnumber={$_SESSION['schedmdnum']}";
		$sqlmcdelete2q = mysql_query($sqlmcdelete2);

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

		$hrs1 = (($sqloriginalfetch[9]-$sqloriginalfetch[7])-
                         ($sqlhrs1a[1]-$sqlhrs1a[0]))
                         /4;
		echo $hrs1;
		$sqlinsert1 = "INSERT INTO monthcal
		               VALUES ( $sqlsamefetch[0],
                                        $sqloriginalfetch[1],
                                        $sqloriginalfetch[2],
                                        $sqloriginalfetch[3],
                                       '$sqloriginalfetch[4]',
					3,
                                        $sqloriginalfetch[10],
                                        '$sqloriginalfetch[6]',
                                        '$sqloriginalfetch[8]')";
                
    $istring = str_replace("'","",$sqlinsert1);            
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);   
    
                mysql_query($sqlinsert1);

		$hrs2 = (($sqlsamefetch[9]-$sqlsamefetch[7])-
                         ($sqlhrs2a[1]-$sqlhrs2a[0]))
                         /4;

		$sqlinsert2 = "INSERT INTO monthcal
		               VALUES ( $sqloriginalfetch[0],
                                        $sqlsamefetch[1],
                                        $sqlsamefetch[2],
                                        $sqlsamefetch[3],
                                       '$sqlsamefetch[4]',
                                        3,
					$sqlsamefetch[10],
                                        '$sqlsamefetch[6]',
                                        '$sqlsamefetch[8]')";
                
    $istring = str_replace("'","",$sqlinsert2);
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);            
                
                mysql_query($sqlinsert2);



                echo '<h2 align="center">The switch has been made.</h2>
                      <br>
                      <h3 align="center">To continue click on Submit.</h3>
                      <br>
		     ';
	    }

	    else if (!empty($sqlsamefetch) && empty($sqloriginalfetch))
	    {
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
   
    $istring = str_replace("'","",$sqlchange2change);                                   
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement); 
    
                $sqlchange1query = mysql_query($sqlchange2);

		$sqldelete1 = "DELETE FROM monthassignment".
		              " WHERE counter=$sqlsamefetch[13]";                
                
                $sqldelete1query = mysql_query($sqldelete1);


		$sqlinsert2 = "INSERT INTO monthassignment".
		              " VALUES ({$_SESSION['schedmdnum']},".
                                        "$sqlsamefetch[1],".
                                        "$sqlsamefetch[2],".
                                        "$sqlsamefetch[3],".
                                       "'$sqlsamefetch[4]',".
                                        "3,".
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
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                                   
                                       
                $file = fopen("log.txt", "a") or exit("Unable to open file!");
                $datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
                $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n". 
                                "User: {$_SESSION['initials']}\n". 
                                "Page: switch_HomeCall.php\n".
                                "Statement: {$sqlinsert2}\n\n";
                fwrite($file, $logStatement);
                fclose($file);                       
                                       
                $sqlinsert2query = mysql_query($sqlinsert2);


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
                                 AND assigntype=3";
		$sqlmcdelete1q = mysql_query($sqlmcdelete1);

		$sqlhrs2 = "SELECT beginblock, endblock
		            FROM assignments
                            WHERE assignment='$sqlsamefetch[4]'
		            AND weekend=$sqlsamefetch[10]";
		$sqlhrs2q = mysql_query($sqlhrs2);
		$sqlhrs2a = @mysql_fetch_row($sqlhrs2q);


		$hrs2 = (($sqlsamefetch[9]-$sqlsamefetch[7])-($sqlhrs2a[1]-$sqlhrs2a[0]))/4;
		$sqlinsert2 = "INSERT INTO monthcal
		               VALUES ({$_SESSION['schedmdnum']},
                                        $sqlsamefetch[1],
                                        $sqlsamefetch[2],
                                        $sqlsamefetch[3],
                                       '$sqlsamefetch[4]',
                                        3,
					$sqlsamefetch[10],
                                        '$sqlsamefetch[6]',
                                        '$sqlsamefetch[8]')";
      
    $istring = str_replace("'","",$sqlinsert2);                           
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'switch_HomeCall.php - {$istring}', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);                           
                               
                $sqlinsert2query = mysql_query($sqlinsert2);



		echo '<h2 align="center">The switch has been made.</h2>
                      <br>
                      <h3 align="center">To continue click on Submit.</h3>
                      <br>
                     ';
	    }
	    else
	    {
	   	echo '<h2 align="center">A problem has been encountered.  No switch was made.</h2>
                      <br>
                      <h3 align="center">To continue click on Submit.</h3>
                      <br>
                     ';
	    }
    }

?>

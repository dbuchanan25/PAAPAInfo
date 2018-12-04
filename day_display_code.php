<?php
/*
 * VERSION 2.1
 * Last Revision 2011-07-20
 */

/*
 * 2011-07-20
 * Separated from day_display, SECTIONS 3 & 4 as a function.
 * Decreased redundant code.
 */
    function day_display_code()
    {
        
        $page_title = 'Day Assignment';
        $_SESSION['schedchange']=1;
        $_SESSION['changeassignment']=0;
        /*
        * Unset all appropriate SESSION variables.
        */
         //day assignment
        if (isset($_SESSION['dayassignment1']))
            unset($_SESSION['dayassignment1']);
        //day assignment 1 begin timeperiod
        if (isset($_SESSION['dayassignment1beginblock']))
            unset($_SESSION['dayassignment1beginblock']);
        //day assignment 1 end timeperiod
        if (isset($_SESSION['dayassignment1endblock']))
            unset($_SESSION['dayassignment1endblock']);
        //day assignment 2
        if (isset($_SESSION['dayassignment2']))
            unset($_SESSION['dayassignment2']);
        //day assignment 2 begin timeperiod
        if (isset($_SESSION['dayassignment2beginblock']))
            unset($_SESSION['dayassignment2beginblock']);
        //day assignment 2 end timeperiod
        if (isset($_SESSION['dayassignment2endblock']))
            unset($_SESSION['dayassignment2endblock']);
        //call assignment 1
        if (isset($_SESSION['callassignment1']))
            unset($_SESSION['callassignment1']);
        //call assignment 1 begin timeperiod
        if (isset($_SESSION['callassignment1beginblock']))
            unset($_SESSION['callassignment1beginblock']);
        //call assignment 1 end timeperiod
        if (isset($_SESSION['callassignment1endblock']))
            unset($_SESSION['callassignment1endblock']);
        //call assignment 2
        if (isset($_SESSION['callassignment2']))
            unset($_SESSION['callassignment2']);
        //call assignment 2 begin timeperiod
        if (isset($_SESSION['callassignment2beginblock']))
            unset($_SESSION['callassignment2beginblock']);
        //call assignment 2 end timeperiod
        if (isset($_SESSION['callassignment2endblock']))
            unset($_SESSION['callassignment2endblock']);
        //added hours 1
        if (isset($_SESSION['ahass1']))
            unset($_SESSION['ahass1']);
        //added hours 2
        if (isset($_SESSION['ahass2']))
            unset($_SESSION['ahass2']);
        //added hours 3
        if (isset($_SESSION['ahass3']))
            unset($_SESSION['ahass3']);
        //beginning time primary assignment
        if (isset($_SESSION['trpbt']))
            unset($_SESSION['trpbt']);
        //beginning time call 1 assignment
        if (isset($_SESSION['trhbt']))
            unset($_SESSION['trhbt']);
        //beginning time call 2 assignment
        if (isset($_SESSION['trhbt2']))
            unset($_SESSION['trhbt2']);
        //ending time primary assignment
        if (isset($_SESSION['trpet']))
            unset($_SESSION['trpet']);
        //ending time call 1 assignment
        if (isset($_SESSION['trhet']))
            unset($_SESSION['trhet']);
        //ending time call 2 assignment
        if (isset($_SESSION['trhet2']))
            unset($_SESSION['trhet2']);
        //reason for adding hours 1
        if (isset($_SESSION['ahreason1']))
            unset($_SESSION['ahreason1']);
        //beginning time for adding hours 1
        if (isset($_SESSION['ah1bt']))
            unset($_SESSION['ah1bt']);
        //ending time for adding hours 1
        if (isset($_SESSION['ah1et']))
            unset($_SESSION['ah1et']);
        //reason for adding hours 2
        if (isset($_SESSION['ahreason2']))
            unset($_SESSION['ahreason2']);
        //beginning time for adding hours 2
        if (isset($_SESSION['ah2bt']))
            unset($_SESSION['ah2bt']);
        //ending time for adding hours 2
        if (isset($_SESSION['ah2et']))
            unset($_SESSION['ah2et']);
        //reason for adding hours 3
        if (isset($_SESSION['ahreason3']))
            unset($_SESSION['ahreason3']);
        //beginning time for adding hours 3
        if (isset($_SESSION['ah3bt']))
            unset($_SESSION['ah3bt']);
        //ending time for adding hours 3
        if (isset($_SESSION['ah3et']))
            unset($_SESSION['ah3et']);




        $mdn = "SELECT number
	        FROM mds
		WHERE initials='{$_SESSION['schedmd']}'";
        $mdnq = mysql_query($mdn);
        $mdnum = @mysql_fetch_row($mdnq);
        $_SESSION['mdn']=$mdnum[0];

        $datet = new DateTime();
        $datet->setDate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Get assignments for primary, secondary, homecall, addhours1(reason),                          //
//addhours2, addhours3 and establish                                                            //
//$_SESSION variables to hold these values                                                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

/*
 * Day assignments are checked.
 *
 * $_SESSION variables are given to:
 * dayassignment1, dayassignment1beginblock, dayassignment1endblock
 * AND
 * dayassignment2, dayassignment2beginblock, dayassignment2endblock
 *
 * If they exist.
 */

        $assign = "SELECT assignment, beginblock, endblock
	            FROM monthassignment
                    WHERE yearnumber={$_SESSION['dty']}
                    AND monthnumber={$_SESSION['dtm']}
                    AND daynumber={$_SESSION['dai']}
                    AND assigntype=1
                    AND mdnumber=(SELECT number
				  FROM mds
                                  WHERE initials='{$_SESSION['schedmd']}')
                    ORDER BY beginblock
                   ";
        $assign2 = mysql_query($assign);
        
        if (mysql_num_rows($assign2) == 2)
        {
            $assign3 = @mysql_fetch_row($assign2);
            $_SESSION['dayassignment1']=$assign3[0];
            $_SESSION['dayassignment1beginblock']=$assign3[1];
            $_SESSION['dayassignment1endblock']=$assign3[2];
                
            $assign3a = @mysql_fetch_row($assign2);
            $_SESSION['dayassignment2']=$assign3a[0];
            $_SESSION['dayassignment2beginblock']=$assign3a[1];
            $_SESSION['dayassignment2endblock']=$assign3a[2];
        }
        else if (mysql_num_rows($assign2) == 1)
        {
            $assign3 = @mysql_fetch_row($assign2);
            $_SESSION['dayassignment1']=$assign3[0];
            $_SESSION['dayassignment1beginblock']=$assign3[1];
            $_SESSION['dayassignment1endblock']=$assign3[2];
        }
        else if (mysql_num_rows($assign2) > 2)
        {
            echo'
            <script>
            alert("There are more than 2 daily assignments! See day_display_code.php");
            </script>';
        }



/*
 * Call assignments are checked.
 * 
 * $_SESSION variables are given to:
 * callassignment1, callassignment1beginblock, callassignment1endblock
 * AND
 * callassignment2, callassignment2beginblock, callassignment2endblock
 *
 * If they exist.
 */
        $hassign = "SELECT assignment, beginblock, endblock
	            FROM monthassignment
                    WHERE yearnumber={$_SESSION['dty']}
                    AND monthnumber={$_SESSION['dtm']}
                    AND daynumber={$_SESSION['dai']}
                    AND assigntype=3
                    AND mdnumber=(SELECT number
				  FROM mds
                                  WHERE initials='{$_SESSION['schedmd']}')
                    ORDER BY beginblock
                   ";
        $hassign2 = mysql_query($hassign);
        
        if (mysql_num_rows($hassign2) == 2)
        {
            $hassign3 = @mysql_fetch_row($hassign2);

            $_SESSION['callassignment1']=$hassign3[0];
            $_SESSION['callassignment1beginblock']=$hassign3[1];
            $_SESSION['callassignment1endblock']=$hassign3[2];

            $hassign3a = @mysql_fetch_row($hassign2);

            $_SESSION['callassignment2']=$hassign3a[0];
            $_SESSION['callassignment2beginblock']=$hassign3a[1];
            $_SESSION['callassignment2endblock']=$hassign3a[2];
        }
        else if (mysql_num_rows($hassign2) == 1)
        {
            $hassign3 = @mysql_fetch_row($hassign2);

            $_SESSION['callassignment1']=$hassign3[0];
            $_SESSION['callassignment1beginblock']=$hassign3[1];
            $_SESSION['callassignment1endblock']=$hassign3[2];
        }
        else if (mysql_num_rows($hassign2) > 2)
        {
            echo'
            <script>
            alert("There are more than 2 call assignments! See day_display_code.php");
            </script>';
        }



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//The SELECT statement to the database returns all the ADDED HOURS entries for a particular day.//
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $ahassign = "SELECT assignment,counter
	             FROM monthassignment
                     WHERE yearnumber={$_SESSION['dty']}
	             AND monthnumber={$_SESSION['dtm']}
                     AND daynumber={$_SESSION['dai']}
                     AND assigntype=4
                     AND mdnumber=(SELECT number
				   FROM mds
                                   WHERE initials='{$_SESSION['schedmd']}')
                    ORDER BY beginblock";
        $ahassign2 = mysql_query($ahassign);


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Added Hours 1 assignment is given to variable $_SESSION['ahass1'] (if it exists)              //
//$_SESSION['ah1count'] gives the primary key for the added hours entry in the database         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $ahassign3 = @mysql_fetch_row($ahassign2);
        if (!empty($ahassign3[0]))
        {
            $_SESSION['ahass1'] = $ahassign3[0];
	    $_SESSION['ah1count']=$ahassign3[1];
        }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Added Hours 2 assignment is given to variable $_SESSION['ahass2'] (if it exists)              //
//$_SESSION['ah2count'] gives the primary key for the added hours entry in the database         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $ahassign3 = @mysql_fetch_row($ahassign2);
        if (!empty($ahassign3[0]))
        {
            $_SESSION['ahass2'] = $ahassign3[0];
	    $_SESSION['ah2count']=$ahassign3[1];
        }

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Added Hours 3 assignment is given to variable $_SESSION['ahass3'] (if it exists)              //
//$_SESSION['ah3count'] gives the primary key for the added hours entry in the database         //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $ahassign3 = @mysql_fetch_row($ahassign2);
        if (!empty($ahassign3[0]))
        {
	    $_SESSION['ahass3'] = $ahassign3[0];
	    $_SESSION['ah3count']=$ahassign3[1];
        }


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the day is a weekday $_SESSION['weekend']=0, if a weekend or holiday $_SESSION['weekend']=1//
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $_SESSION['weekend']=isweekend($datet);



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Begin and end blocks are obtained from table monthassignment for Primary & HomeCall           //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        /*
        $block3 = "SELECT beginblock, endblock
	           FROM monthassignment
                   WHERE yearnumber={$_SESSION['dty']}
                   AND monthnumber={$_SESSION['dtm']}
                   AND daynumber={$_SESSION['dai']}
                   AND assigntype=1
                   AND mdnumber=(SELECT number
				 FROM mds
                                 WHERE initials='{$_SESSION['schedmd']}')
                  ";
        $block2 = mysql_query($block3);
        $block = @mysql_fetch_row($block2);



        $hblock3 = "SELECT beginblock, endblock
                    FROM monthassignment
                    WHERE yearnumber={$_SESSION['dty']}
                    AND monthnumber={$_SESSION['dtm']}
                    AND daynumber={$_SESSION['dai']}
                    AND assigntype=3
                    AND mdnumber=(SELECT number
				  FROM mds
                                  WHERE initials='{$_SESSION['schedmd']}')
                    ORDER BY beginblock
                   ";
        $hblock2 = mysql_query($hblock3);
        $hblock = @mysql_fetch_row($hblock2);
         * 
         */


/*
 * Call the "day_display_code1.php" function.
 */
        day_display_code1($datet);


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Usual Begin and end blocks are obtained from table assignments                                //
//for Primary & Call                                                                            //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        if (isset($assign3)){
            $b3 = "SELECT beginblock, endblock
                   FROM assignments
                   WHERE weekend={$_SESSION['weekend']}
                   AND assignment='{$assign3[0]}'";
            $b2 = mysql_query($b3);
            $b = @mysql_fetch_row($b2);
        }


        if (!empty($hassign3))
        {
            $hb3 = "SELECT beginblock, endblock
	            FROM assignments
                    WHERE weekend={$_SESSION['weekend']}
	            AND assignment='{$hassign3[0]}'";
            $hb2 = mysql_query($hb3);
            $hb = @mysql_fetch_row($hb2);
            /*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Queries for time results for call assignment                                                  //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/
            if (mysql_num_rows($hb2) > 0)
            {
                $tr5=mysql_query("SELECT time, timeperiod
                                  FROM timeperiods
                                  WHERE timeperiod>=$hb[0]
                                  AND timeperiod<$hb[1]");

                $tr6=mysql_query("SELECT time
                                  FROM timeperiods
                                  WHERE timeperiod>$hb[0]
                                  AND timeperiod<=$hb[1]");
            }

        }





/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//THE FORM CODE OF THE PAGE IS BELOW                                                            //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Queries for time results for primary assignment                                               //
//////////////////////////////////////////////////////////////////////////////////////////////////
*/
        $tr1=mysql_query("SELECT time
	                  FROM timeperiods
                          WHERE timeperiod>=$b[0]
                          AND timeperiod<$b[1]");

        $tr2=mysql_query("SELECT time
	                  FROM timeperiods
                          WHERE timeperiod>$b[0]
                          AND timeperiod<=$b[1]");




/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Queries for time results for add hours                                                        //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
        $timeresult7=mysql_query('SELECT time, timeperiod
	                          FROM timeperiods');
        $timeresult8=mysql_query('SELECT time, timeperiod
	                          FROM timeperiods');;
        $timeresult9=mysql_query('SELECT time, timeperiod
	                          FROM timeperiods');
        $timeresult10=mysql_query('SELECT time, timeperiod
	                           FROM timeperiods');
        $timeresult11=mysql_query('SELECT time, timeperiod
	                           FROM timeperiods');
        $timeresult12=mysql_query('SELECT time, timeperiod
	                           FROM timeperiods');

        $trpbtquery = "SELECT beginblock,endblock
	               FROM monthassignment
                       WHERE yearnumber={$_SESSION['dty']}
	               AND monthnumber={$_SESSION['dtm']}
                       AND daynumber={$_SESSION['dai']}
                       AND assigntype=1
                       AND mdnumber=(SELECT number
                                     FROM mds
                                     WHERE initials='{$_SESSION['schedmd']}')
                      ";
        $timeresultpribt=mysql_query($trpbtquery);
        $trpbtt = @mysql_fetch_row($timeresultpribt);
        if (!empty($trpbtt))
        {
            $_SESSION['trpbt']=$trpbtt[0];
            $_SESSION['trpet']=$trpbtt[1];
        }
        $trpbtt2 = @mysql_fetch_row($timeresultpribt);
	if (!empty($trpbtt2))
        {
            $_SESSION['trpbt2']=$trpbtt2[0];
            $_SESSION['trpet2']=$trpbtt2[1];
        }

        $trhbtquery = "SELECT beginblock, endblock
                       FROM monthassignment
                       WHERE yearnumber={$_SESSION['dty']}
                       AND monthnumber={$_SESSION['dtm']}
                       AND daynumber={$_SESSION['dai']}
                       AND assigntype=3
                       AND mdnumber=(SELECT number
                                     FROM mds
                                     WHERE initials='{$_SESSION['schedmd']}')
                       ORDER BY beginblock
                      ";
        $timeresulthomebt=mysql_query($trhbtquery);
        $trhbtt = @mysql_fetch_row($timeresulthomebt);
        if (!empty($trhbtt))
        {
            $_SESSION['trhbt']=$trhbtt[0];
            $_SESSION['trhet']=$trhbtt[1];
        }

	$trhbtt2 = @mysql_fetch_row($timeresulthomebt);
	if (!empty($trhbtt2))
        {
            $_SESSION['trhbt2']=$trhbtt2[0];
            $_SESSION['trhet2']=$trhbtt2[1];
        }


        //primaryassign   ($assign3[0], $trpbtt[0], $trpbtt[1]);
        dayassign ( $_SESSION['dayassignment1'], $trpbtt[0], $trpbtt[1],
                    $_SESSION['dayassignment2'], $trpbtt2[0], $trpbtt2[1]);
        callassign ($_SESSION['callassignment1'], $trhbtt[0], $trhbtt[1],
                    $_SESSION['callassignment2'], $trhbtt2[0], $trhbtt2[1]);
        /*
         * $dbc2 is the database connection
         */
        addhours ();
        splitAssignment();

        echo '
                <br><br>
                </table>
                <br><br>
             ';
        include ('includes/footer.html');
    }

?>

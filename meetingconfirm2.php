<?php /*if (!isset($_SESSION)) { session_start(); } *  *//*Check to see is the user is logged in*//*if (!isset($_SESSION['initials'])){   require_once ('includes/login_functions.inc.php');   $url = absolute_url();   header("Location: $url");   exit();}else{ *  */    echo 'Here';   /*   var_dump($_REQUEST);      $page_title = 'Meeting Confirmation';   echo '<title>'.$page_title.'</title>';      require_once ($_SESSION['login2string']);      $dimo = cal_days_in_month(CAL_GREGORIAN, $_REQUEST['month'], $_REQUEST['yr']);   var_dump($dimo);      $dateString = $_REQUEST['yr'].'-'.$_REQUEST['month'].'-'.$_REQUEST['dae'];         $datetimeMeeting = new DateTime($dateString);      $datetimeToday = new DateTime(("now"), new DateTimeZone('America/New_York'));         $checkRepeatMeetingStatement = "SELECT * ".                                  "FROM meetings ".                                  "WHERE mdinitials LIKE '".$_SESSION['initials'].                                  "' AND yearnumber = ".$_REQUEST['yr'].                                  " AND monthnumber = ".$_REQUEST['month'].                                  " AND daynumber = ".$_REQUEST['dae'].                                  " AND begintimeperiod = ".$_REQUEST['bt'];    var_dump($checkRepeatMeetingStatement);   $checkRepeatMeetingQuery = mysql_query($checkRepeatMeetingStatement);         if ($datetimeMeeting < $datetimeToday)   {       echo '                <h2>                <center>                You have made a mistake in choosing the date.                <br>'.                $datetimeMeeting->format('Y-m-d').' is before today.                <br>                Please recheck your date and resubmit the information.                </center>                </h2>';       echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="meetingnotifi.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }      else if ($_REQUEST['dae'] > $dimo)   {       echo '                <h2>                <center>                You have made a mistake in choosing the date.                <br>                There is not a day '.$_REQUEST['dae'].' in that month.                <br>                Please recheck your date and resubmit the information.                </center>                </h2>';       echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="meetingnotifi.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }      else if ($_REQUEST['bt']>=$_REQUEST['et'])   {       echo '                <h2>                <center>                You have made a mistake in choosing the beginning and ending times.                <br>                Please recheck your times and resubmit the information.                </center>                </h2>';       echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="meetingnotifi.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }   else if (stristr($_REQUEST['commnt'],"Enter Meeting Reason & Comments Here.")!=FALSE)   {       echo '                <h2>                <center>                You did not enter reason/comments appropriately for the meeting.                <br>                Please add a reason and any comments and then resubmit the information.                <br>                Be sure the erase the current "Enter Meeting Reason & Comments Here." when                doing so.                </center>                </h2>';       echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="meetingnotifi.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }      else if (mysql_num_rows($checkRepeatMeetingQuery) > 0)   {              echo '                <h2>                <center>                You already have a meeting for this day and time.                <br>                You may have only one meeting for a specific day and start time.                </center>                </h2>';       echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="meetingnotifi.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }      else   {            $meetingStatement = "INSERT INTO meetings ".                        "VALUES (".                        "'{$_SESSION['initials']}', ".                        "{$_REQUEST['yr']}, ".                        "{$_REQUEST['month']}, ".                        "{$_REQUEST['dae']}, ".                        "{$_REQUEST['bt']}, ".                        "{$_REQUEST['et']}, ".                        "'{$_REQUEST['commnt']}', ".                        "NULL)";    var_dump($meetingStatement);                           $istring = str_replace("'","",$meetingStatement);        $mdentryStatement = "INSERT INTO mdlog ".                        "VALUES (".                        "'{$_SESSION['initials']}', ".                        "'{$_SESSION['initials']}', ".                        "'meetingconfirm.php - {$istring}', ".                        "CURRENT_TIMESTAMP, ".                        "NULL".                        ")";       var_dump($mdentryStatement);    mysql_query($mdentryStatement);                                                $file = fopen("log.txt", "a") or exit("Unable to open file!");    $logStatement = "Date/Time: {$datetimeToday->format('Y-m-d H:i:s')}\n".                    "User: {$_SESSION['initials']}\n".                    "Page: meetingconfirm.php\n".                   "Statement: {$meetingStatement}\n\n";    fwrite($file, $logStatement);    fclose($file);    mysql_query($meetingStatement);              echo '               <h2>               <center>               Your meeting has been recorded and will show up on the ORMGR Worksheet for that day               </center>               </h2>';   echo '               <br><br>               <center>               <FORM METHOD="LINK" ACTION="choose.php">               <INPUT TYPE="submit" VALUE="Submit">               </FORM>               <br>               <br>              ';   }    *     *///}?>
<?php
session_start();

/*
 * Version 2_1
 * Last Revised 2011-08-22
 */
/*
 * Revised 2011-08-22 to include the menu bar across the top so a user can go back to the program
 * without having to execute the split.
 */
/*
 * 2011-07-25 Written for the new pay rules now going into effect.
 * Want to make it easy to split an assignment between partners.
 *
 * splitAssignment.php leads to this file and provides the $_REQUEST['assignmentSplit']
 * variable value.
 *
 * $_SESSION['assignmentSplit']=0 if a Day Assignment Split, =1 if a Call Assignment Split.
 * Setting $_SESSION['docs'] just shows the respective physicians have been chosen.
 * $_REQUEST['partnerHoldingAssignment'] gives the number of the physician holding the assignment
 *    to be split.
 * $_REQUEST['partnerGettingAssignment'] gives the number of the physician getting part of the
 *    assignment being split.
 * $_SESSION['split'] is set when the assignment is chosen between multiple assignments possible
 *    ie multiple day or call assignments.
 * $_SESSION['primaryAssignmentSplit'] is the day assignment to be split if there is only one
 *    such assignment.
 * $_SESSION['callAssignmentSplit'] is the call assignment to be split if there is only one such
 *    assignment.
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
    require_once ($_SESSION['login2string']);
    include ('includes/header.php');
    include ('split2.php');

/*
 * First time through, get the doctors involved, set $_SESSION['assignmentSplit'], then send it
 * back to split.php
 * $_SESSION['docs'] get set to true when the physicians are chosen.
 */
    echo'
        <div class="menu2">
	<table align="center" class="menu2" border="1" bordercolor="#D7DAE1"
                                                       width="100%" bgcolor="#E5E5E5">
		<tr align="center">
			<td align="center" height="25px" bordercolor="#808080">
			                           <a href="choose.php">Schedule For Page</a></td>
			<td align="center" height="25px" bordercolor="#808080">
			                      <a href="monthcalendar2.php">Complete Month</a></td>
			<td align="center" height="25px" bordercolor="#808080">
			                            <a href="ormpage.php">ORMGR Worksheet</a></td>
   			<td align="center" height="25px" bordercolor="#808080">
			                                      <a href="logout.php">Logout</a></td>
		</tr>
	</table>
	</div>
        <br>
        <br>
        <br>';

    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'split.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
    
    if (!isset($_SESSION['docs']))
    {
        if ($_REQUEST['assignmentSplit']=="Day Assignment")
            $_SESSION['assignmentSplit']=0;
        else
            $_SESSION['assignmentSplit']=1;

        echo '<center><h2>Choose Involved Partners in Assignment Split</h2></center><br><br>';
        
        $possibleDocsStatement = "SELECT last, first, number
                                  FROM mds
                                  ORDER BY last";
        $possibleDocsQuery = mysql_query($possibleDocsStatement);
        echo '  <form method="post" action="split.php">
                        <table align="left" width="100%" border="0">
                            <tr>
                                <td width="15%">
                                </td>

                                <td align="right" height="25" width="40%">
                                Partner Who <u>Currently</u> Has the Assignment To Be Split:
                                </td>

                                <td width="3%">
                                </td>

                                <td align="left" height="25" width="42%">
                                <select name="partnerHoldingAssignment">
              ';
        while ($holdingMD = mysql_fetch_array($possibleDocsQuery))
        {
            if ($holdingMD['number']==$_SESSION['mdn'])
                echo "<option value={$holdingMD['number']} selected='selected'>
                            {$holdingMD['last']}, {$holdingMD['first']}</option>";
            else 
                echo "<option value={$holdingMD['number']}>
                            {$holdingMD['last']}, {$holdingMD['first']}</option>";
        }
        echo  '                 </select>
                                </td>
                            </tr>
                       </table>
                       <br><br>
              ';


        $possibleDocsStatement2 = "SELECT last, first, number
                                   FROM mds
                                   ORDER BY last";
        $possibleDocsQuery2 = mysql_query($possibleDocsStatement2);
        echo '         <table align="left" width="100%" border="0">
                            <tr>
                                <td width="15%">
                                </td>

                                <td align="right" height="25" width="40%">
                                Partner Who Is <u>Getting</u> Part of the Assignment:
                                </td>

                                <td width="3%">
                                </td>

                                <td align="left" height="25" width="42%">
                                <select name="partnerGettingAssignment">
              ';
        while ($gettingMD = mysql_fetch_array($possibleDocsQuery2))
        {
            if ($gettingMD['number']==$_SESSION['mdn'])
                echo "<option value={$gettingMD['number']} selected='selected'>
                            {$gettingMD['last']}, {$gettingMD['first']}</option>";
            else
                echo "<option value={$gettingMD['number']}>
                            {$gettingMD['last']}, {$gettingMD['first']}</option>";
        }
        $_SESSION['docs'] = true;
        echo  '                 </select>
                                </td>
                            </tr>
                       </table>
                       <br><br><br><br>
                       <table width="100%">
                            <tr>
                                <td align="center">
                                <input type="submit" style="width:175px; height:25px;
                                font-size:medium" name="submit" value="Submit Changes" />
                                </td>
                            </tr>
                       </table>
                   </form>
                   <br><br>
              ';

    }

    /*
     * Second time through split.php comes here with $_SESSION['split'] being "true"
     * Thus, the "holding" doctor and the "getting" doctor are identified
     * If this page has been visited already then $_SESSION['split'] is set to "true"
     */
    else if (!isset($_SESSION['split']) && isset($_SESSION['docs']))
    {

        $_SESSION['partnerHoldingAssignment']=$_REQUEST['partnerHoldingAssignment'];
        $_SESSION['partnerGettingAssignment']=$_REQUEST['partnerGettingAssignment'];
        
        if ($_SESSION['partnerHoldingAssignment']==$_SESSION['partnerGettingAssignment'])
        {
            echo '<center><h2>There has been an error in choosing the proper partners!
                  </h2><br><center>You need to try again.</center>';
            unset($_SESSION['partnerHoldingAssignment']);
            unset($_SESSION['partnerGettingAssignment']);
            unset($_SESSION['assignmentCounter']);
            unset($_SESSION['docs']);
            echo '<center>
                  <br><br>
                  <FORM METHOD="LINK" ACTION="day_display.php">
                  <INPUT TYPE="submit" VALUE="Submit">
                  </FORM>
                  </center>';
        }
    /*
     * Splitting a Day Assignment
     * $_SESSION['assignmentSplit']==0 if it is a Day Assignment getting split
     */
        else if ($_SESSION['assignmentSplit']==0)
        {
            $_SESSION['splitAssignment']=0;
            $currentPrimaryAssignment = "SELECT *
                                         FROM monthassignment
                                         WHERE monthnumber={$_SESSION['dtm']}
                                         AND daynumber={$_SESSION['dai']}
                                         AND yearnumber={$_SESSION['dty']}
                                         AND mdnumber={$_SESSION['partnerHoldingAssignment']}
                                         AND assigntype=1
                                         ORDER BY beginblock";
            $currentPrimaryAssignmentQuery = mysql_query($currentPrimaryAssignment);
            $number_rows=@mysql_num_rows($currentPrimaryAssignmentQuery);
            if ($number_rows==0)
            {
                echo '<h2><center>There are no assignments for that partner for that criteria.
                      </h2><br>You need to try again.</center>.';
                unset($_SESSION['partnerHoldingAssignment']);
                unset($_SESSION['partnerGettingAssignment']);
                unset($_SESSION['assignmentCounter']);
                unset($_SESSION['docs']);
                echo '<br><br>
                      <center>
                      <FORM METHOD="LINK" ACTION="day_display.php">
                      <INPUT TYPE="submit" VALUE="Submit">
                      </FORM>
                      </center>';
            }

            else if ($number_rows==1)
            {
                $currentPrimaryAssignmentResult =
                    @mysql_fetch_array($currentPrimaryAssignmentQuery);
                $_SESSION['assignmentCounter']=$currentPrimaryAssignmentResult['counter'];

                split2();
            }

            else
            {
                echo '  <form method="post" action="split.php">
                        <table align="left" width="100%" border="0">
                            <tr>
                                <td width="30%">
                                </td>
                                
                                <td align="right" height="25" width="20%">
                                Primary Assignment to be Split:
                                </td>

                                <td width="5%">
                                </td>

                                <td align="left" height="25" width="45%">
                                <select name="dayAssignmentSplit">
                     ';
                while ($currentPrimaryAssignmentResult =
                        @mysql_fetch_array($currentPrimaryAssignmentQuery))
                {
                    echo "<option value={$currentPrimaryAssignmentResult['counter']}>
                            {$currentPrimaryAssignmentResult['assignment']}</option>";
                }
                echo  '         </select>
                                </td>
                            </tr>
                       </table>
                       <br><br>
                       <table width="100%">
                            <tr>
                                <td align="center">
                                <input type="submit" style="width:175px; height:25px;
                                font-size:medium" name="submit" value="Submit Changes" />
                                </td>
                            </tr>
                       </table>
                       </form>
                       <br><br>
                      ';
                $_SESSION['split']=true;
            }
        }



        /*
         * Splitting a Call Assignment
         */
        else if ($_SESSION['assignmentSplit']==1)
        {
            $currentCallAssignment = "SELECT *
                                      FROM monthassignment
                                      WHERE monthnumber={$_SESSION['dtm']}
                                      AND daynumber={$_SESSION['dai']}
                                      AND yearnumber={$_SESSION['dty']}
                                      AND mdnumber={$_SESSION['partnerHoldingAssignment']}
                                      AND assigntype=3
                                      ORDER BY beginblock";
            $currentCallAssignmentQuery = mysql_query($currentCallAssignment);
            $number_rows=@mysql_num_rows($currentCallAssignmentQuery);

            if ($number_rows==0)
            {
                echo '<h2><center>There are no assignments for that partner for that criteria.
                      </h2><br><center>You need to try again.</center>.';
                unset($_SESSION['partnerHoldingAssignment']);
                unset($_SESSION['partnerGettingAssignment']);
                unset($_SESSION['assignmentCounter']);
                unset($_SESSION['docs']);
                echo '<center>
                      <FORM METHOD="LINK" ACTION="day_display.php">
                      <INPUT TYPE="submit" VALUE="Submit">
                      </FORM>
                      </center>';
            }
            else if ($number_rows==1)
            {
                $currentCallAssignmentResult =
                    @mysql_fetch_array($currentCallAssignmentQuery);
                $_SESSION['assignmentCounter']=$currentCallAssignmentResult['counter'];

                split2();
            }
            else 
            {
                echo '
                      <form method="post" action="split.php">
                      <table width="100%">
                        <tr>
                            <td width="30%">
                            </td>

                            <td align="right" height="25" width="20%">
                            Call Assignment to be Split:
                            </td>

                            <td width="5%">
                            </td>

                            <td align="left" height="25" width="45%">
                            <select name="callAssignmentSplit">';
                while($currentCallAssignmentResult =
                        @mysql_fetch_array($currentCallAssignmentQuery))
                {
                    echo "<option value={$currentCallAssignmentResult['counter']}>
                            {$currentCallAssignmentResult['assignment']}</option>";
                }
                echo '      </td>
                        </tr>
                     </table>
                     <br><br>
                     <table width="100%">
                        <tr>
                            <td align="center">
                            <input type="submit" style="width:175px; height:25px;
                            font-size:medium" name="submit" value="Submit Changes" />
                            </td>
                        </tr>
                      </table>
                      </form>
                      <br><br>
                            ';
                $_SESSION['split']=true;
            }
        }
    }
    else
    {
         if (isset($_SESSION['split']))
             unset($_SESSION['split']);

         /*
          * $_SESSION['assignmentCounter'] is the number of the assignment being split.
          * This depends whether the assignment is a day or a call assignment.
          */
         if ($_SESSION['assignmentSplit']==0)
             $_SESSION['assignmentCounter']=$_REQUEST['dayAssignmentSplit'];
         else
             $_SESSION['assignmentCounter']=$_REQUEST['callAssignmentSplit'];
         split2();
    }
    echo '<br><br>';
    include ('includes/footer.html');
}

?>

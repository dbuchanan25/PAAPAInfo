<?php
if (!isset($_SESSION)) { session_start(); }

/*
 * VERSION 02_02
 */
/*
//REVISED 2011-04-02 TO FORMAT BETTER FOR PRINTING AND GET THE "WORKING AFTER CALL" OPTION
//AVAILABLE
 */
/*
 * REVISED 2011-06-28 TO BETTER FORMAT AND TO LOOK AT THE NEED FOR ADDITIONAL CODING TO GET THE
 * APPROPRIATE HOURS ADDED FOR PEOPLE STAYING AFTER CALL.
 */
/*
 * REVISED 2015-07-02 TO ACCOMODATE 'BUSINESS' AS A POSSIBLE ADDED HOURS REASON
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
    include       ('includes/header.php');
    require_once ($_SESSION['login2string']);

    include       ('day_display_code1.php');
    include       ('day_display_code2.php');

    include       ('isweekend.php');



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
// THIS IS THE ADD HOURS CHANGE PAGE                                                            //
// First, display the day's assignment.                                                         //
// Then display the choices for added hours depending on what is already in the database.       //
// Make it possible to add up to 3 different periods of time for added hours.                   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */

    $page_title = 'Added Hours';
    
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','{$_SESSION['schedmd']}', 'add.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);



    $datet = new DateTime();
    $datet->setDate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);
    $_SESSION['specialadd']=0;


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the day is a weekday $_SESSION['dtb']=0, if a weekend $_SESSION['dtb']=1                   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
   /*
    * Is this really needed?  Does it need to be changed??????????????????????????????????????????
    */
    $_SESSION['dtb']=isweekend($datet);



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Begin and end blocks are obtained from table monthassignment for Primary & HomeCall           //
//////////////////////////////////////////////////////////////////////////////////////////////////
 */
    $block3 = "SELECT beginblock, endblock, assignment 
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
    $block  = @(mysql_fetch_array($block2));

    $block8 = "SELECT type 
               FROM assignments 
               WHERE assignment='{$block['assignment']}'";
    $block9 = mysql_query($block8);
    $blockln  = @(mysql_fetch_array($block9));



    $hblock3 = "SELECT beginblock, endblock, assignment 
                FROM monthassignment 
                WHERE yearnumber={$_SESSION['dty']}
                AND monthnumber={$_SESSION['dtm']} 
                AND daynumber={$_SESSION['dai']} 
                AND assigntype=3 
                AND mdnumber=(SELECT number 
                              FROM mds 
                              WHERE initials='{$_SESSION['schedmd']}')
               ";
    $hblock2 = mysql_query($hblock3);
    $hblock  = @mysql_fetch_array($hblock2);

    $dayno = $_SESSION['dai']-1;
    $monthno = $_SESSION['dtm'];
    $yearno = $_SESSION['dty'];
    if ($dayno==0)
    {
        $monthno = $_SESSION['dtm']-1;
        if ($monthno == 0)
        {
                $yearno = $_SESSION['dty']-1;
                $monthno = 12;
                $dayno = 31;
        }
        else
        {
                $dayno = cal_days_in_month ( CAL_GREGORIAN , $monthno , $yearno);
        } 
    }
    $lastnightq     =   "SELECT assignment 
                         FROM monthassignment 
                         WHERE yearnumber=$yearno  
                         AND monthnumber=$monthno 
                         AND daynumber=$dayno 
                         AND assigntype=3
                         AND mdnumber=(SELECT number 
                                       FROM mds 
                                       WHERE initials='{$_SESSION['schedmd']}')
                        ";
    $lastnightquery = mysql_query($lastnightq);
    $lastnight      = @mysql_fetch_array($lastnightquery);
    $ln             = trim($lastnight['assignment']);

    /*
     * This may need to be changed to better accomodate the proper intent?????????????????????????
     */
    $calllastnight = 0;
    if ($ln=='C OR' || $ln=='C OB' || $ln=='C Mat' || $ln=='C Hnt' || $ln=='C OH')
    {
          $calllastnight=1;
          $_SESSION['calllastnight']=1;
    }



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Go to the daydisplay function to construct the part of the page which displays the day's      //
//assignment.                                                                                   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * This was renamed to day_display_code1() on 2011-07-20
 */
    day_display_code1($datet);

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Begin the code which allows manipulation of the added hours.                                  //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    echo '<form method="post" action="reasonAH.php">';
   
   
/*
 * THIS SHOULD UNNECESSARY WITH THE NEW RULES
 * IN FACT, THE RULES WILL CHANGE TO ALLOW 1.3 HOURS/HOUR WHEN SOMEONE IS WORKING AFTER CALL
 * WHEN PAA ASSIGNS THE PARTNER TO DO SO.
 * ///////////////////////////////////////////////////////////////////////////////////////////////
 * Make it so anyone working after call has to enter the number of hours they actually worked to
 * get credit for those hours.
 *
 * Make the primary assignment a non-credited assignment for S Eye WAC, Ops 2 WAC, S Rad WAC,
 * Smat2 WAC, Shnt2 WAC with the usual hours when the partner has a call assignment the previous
 * night.  This gets assigned at the beginning of the month when the schedule comes out.  Any
 * changes that partners make after that such that they are working after call will be taken to
 * not be at PAAPA request and would pay per the usual assignment.
 */

 /*
  * This most likely will need to be changed to accomplish the intent.????????????????????????????
  */
   if ($calllastnight==1 && $blockln[0]=='WEEKDAYPC')
   {
?>
		<script type="text/javascript">
    		alert("If you are working after call you need to choose as your reason\n"+
                      "for adding hours as WORKING AFTER CALL AT PAAPA REQUEST to get\n"+
                      "credit for working after call.");
    		</script>
<?php


        if (!empty($hblock) && trim($hblock['assignment'])=='C OH')
        {
            $additionalReason = " OR assignment LIKE 'C OH'";
        }
        else if (!empty($hblock) && trim($hblock['assignment'])=='Peds Call')
        {
            $additionalReason = " OR assignment LIKE 'Peds'";
        }
        else if (!empty($hblock) && trim($hblock['assignment'])=='H 1')
        {
            $additionalReason = " OR assignment LIKE 'H 1'";
        }
        else
        {
            $additionalReason = "";
        }

            $result4Statement=  ("SELECT DISTINCT assignment,n
	                         FROM assignments
	                         WHERE addhours=1
	                         AND weekend={$_SESSION['dtb']}
	                         AND(assignment LIKE 'Working After Call at PAAPA Request'
                                  OR assignment LIKE 'Business'
                                  OR assignment LIKE 'None'
                                  OR assignment LIKE 'Other'
                                  OR assignment LIKE 'Meeting Coverage'$additionalReason)
	                         ORDER BY assignment");
            $result4 = mysql_query($result4Statement);
            $result5=mysql_query("SELECT DISTINCT assignment,n
	                         FROM assignments
	                         WHERE addhours=1
	                         AND weekend={$_SESSION['dtb']}
	                         AND(assignment LIKE 'Working After Call at PAAPA Request'
                                  OR assignment LIKE 'Business'
                                  OR assignment LIKE 'None'
                                  OR assignment LIKE 'Other'
                                  OR assignment LIKE 'Meeting Coverage'$additionalReason)
	                         ORDER BY assignment");
	    $result6=mysql_query("SELECT DISTINCT assignment,n
	                         FROM assignments
	                         WHERE addhours=1
	                         AND weekend={$_SESSION['dtb']}
	                         AND(assignment LIKE 'Working After Call at PAAPA Request'
                                  OR assignment LIKE 'Business'
                                  OR assignment LIKE 'None'
                                  OR assignment LIKE 'Other'
                                  OR assignment LIKE 'Meeting Coverage'$additionalReasonb)
	                         ORDER BY assignment");
   }

   else if ($hblock[2]=='C OH')
   {
   	   $_SESSION['specialadd']=1;
	   $result4=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND(assignment='C OH'
                                  OR assignment LIKE 'Business'
                                  OR assignment='None' 
                                  OR assignment='Other')
	                         ORDER BY assignment");
	   $result5=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND(assignment='C OH'
                                  OR assignment LIKE 'Business'
                                  OR assignment='None' 
                                  OR assignment='Other')
	                         ORDER BY assignment");
	   $result6=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND(assignment='C OH'
                                  OR assignment LIKE 'Business'
                                  OR assignment='None' 
                                  OR assignment='Other')
	                         ORDER BY assignment");
   }
   else if ($hblock[2]=='Peds Call')
   {
   	   $_SESSION['specialadd']=2;
	   $result4=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='Peds'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                           OR assignment='Assignment Overrun'
	                           OR assignment='Meeting Coverage'
	                             )
	                         ORDER BY assignment");
	   $result5=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='Peds'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                           OR assignment='Assignment Overrun'
	                           OR assignment='Meeting Coverage'
	                             )
	                         ORDER BY assignment");
	   $result6=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='Peds'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                           OR assignment='Assignment Overrun'
	                           OR assignment='Meeting Coverage'
	                             )
	                         ORDER BY assignment");
   }
   else if ($hblock[2]=='H 1')
   {
   	   $_SESSION['specialadd']=3;
	   $result4=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='H 1'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                             )
	                         ORDER BY assignment");
	   $result5=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='H 1'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                             )
	                         ORDER BY assignment");
	   $result6=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1  
	                         AND weekend={$_SESSION['dtb']} 
	                         AND (assignment='H 1'
                                   OR assignment LIKE 'Business'
	                           OR assignment='None' 
	                           OR assignment='Other'
	                             )
	                         ORDER BY assignment");
   }
   else
   {
	   $result4=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1
	                         AND  weekend={$_SESSION['dtb']}
                                 AND assignment NOT LIKE 'Working After Call%'
	                         ORDER BY assignment");
	   $result5=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1
	                         AND  weekend={$_SESSION['dtb']}
                                 AND assignment NOT LIKE 'Working After Call%'
	                         ORDER BY assignment");
	   $result6=mysql_query("SELECT DISTINCT assignment,n 
	                         FROM assignments 
	                         WHERE addhours=1
	                         AND  weekend={$_SESSION['dtb']}
                                 AND assignment NOT LIKE 'Working After Call%'
	                         ORDER BY assignment");
   }

   $timeresult7 =mysql_query('Select time, timeperiod from timeperiods');
   $timeresult8 =mysql_query('Select time, timeperiod from timeperiods where timeperiod > 3');
   $timeresult9 =mysql_query('Select time, timeperiod from timeperiods');
   $timeresult10=mysql_query('Select time, timeperiod from timeperiods where timeperiod > 3');
   $timeresult11=mysql_query('Select time, timeperiod from timeperiods');
   $timeresult12=mysql_query('Select time, timeperiod from timeperiods where timeperiod > 3');

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//First loop                                                                                    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */
    echo'
        <br>
	<br>
        <table align="center" class="content" border="0" width="100%" bordercolor="#000000">
           <tr>
                <td width="225px" align="center"></td>
                <td align="center" height="25" width="100" style="color:black">
                    Added Hours:
	        </td>
	        <td align="center" height="25" width="200" style="color:black">
	        <select name="reasonaddedhours1" style="width:200px">
        ';
}


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If added hours haven't been added..........Make it possible to add.................           //
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

if (!isset($_SESSION['ahreason1']))
{
    while($row12 = @mysql_fetch_array($result4))
    {
        if ($row12['assignment']=='None')
            echo "<option selected='selected' value={$row12['n']}>
                        {$row12['assignment']}</option>\n";
        else
            echo "<option value={$row12['n']}>{$row12['assignment']}</option>\n";
    }
    
    echo'
                </td>
                <td align="right" height="25" width="100" style="color:black">
                    Begin Time:
	        </td>
	        <td align="center" height="25" width="100" style="color:black">
	        <select name="btaddedhours1">
        ';
    
    while($row10 = @mysql_fetch_array($timeresult7))
    {
        echo "<option value=$row10[1]>$row10[0]</option>\n";
    }

    echo'
                </select></td>
                <td align="right" height="25" width="100" style="color:black">
                    End Time:
                </td>
	        <td align="center" height="25" width="100" style="color:black">
                <select name="etaddedhours1">
        ';
		
    while($row11 = @mysql_fetch_array($timeresult8))
    {
        echo "<option value=$row11[1]>$row11[0]</option>\n";
    }
   
    echo'
                </select>
                </td>	  
                <td width="225px" align="center">
                </td>
            </tr>
        </table>
        ';
}

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If they have been added........Round 1.....Place the results and make it possible to add more.//
//////////////////////////////////////////////////////////////////////////////////////////////////
 *
 */

else
{
    while($row12 = @mysql_fetch_array($result4))
    {
        if ($row12['assignment']==$_SESSION['ahreason1'])
            echo "<option selected='selected' value={$row12['n']}>
                    {$row12['assignment']}</option>\n";
	else
            echo "<option value={$row12['n']}>{$row12['assignment']}</option>\n";
    }
   
    echo'
                </select>
		</td>
                <td align="right" height="25" width="100" style="color:black">
                    Begin Time:
                </td>
                <td align="center" height="25" width="100" style="color:black">
                <select name="btaddedhours1">
	';
	   
    while($row10 = @mysql_fetch_array($timeresult7))
    {
        if (trim($row10['timeperiod'])==$_SESSION['ah1bt'])
            echo "<option selected='selected' value=$row10[1]>$row10[0]</option>\n";
	else
            echo "<option value={$row10['timeperiod']}>{$row10['time']}</option>\n";
    }

    echo'
                </select>
                </td>
                <td align="right" height="25" width="100" style="color:black">
                    End Time:
                </td>
                <td align="center" height="25" width="100" style="color:black">
                <select name="etaddedhours1">
        ';
	   
    while($row11 = @mysql_fetch_array($timeresult8))
    {
        if ($row11['timeperiod']==$_SESSION['ah1et'])
            echo "<option selected='selected' value={$row11['timeperiod']}>
                        {$row11['time']}</option>\n";
	else
            echo "<option value={$row11['timeperiod']}>{$row11['time']}</option>\n";
   }
   
    echo'
                </select>
                </td>
   	        <td width="225px" align="center">
		</td>
            </tr>
        ';

		 
/*
//////////////////////////////////////////////////////////////////////////////////////////////////   
//Test again for more results, if there, display them and make it possible to add more...Part 2 //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
   
    if (isset($_SESSION['ahreason2']))
    {
        echo'
                <tr>
                    <td width="225px" align="center"></td>
                    <td align="center" height="25" width="100" style="color:black">
                        Added Hours:
                    </td>
                    <td align="center" height="25" width="200" style="color:black">
                    <select name="reasonaddedhours2" style="width:200px" value=$row12[1]>
            ';

            while($row12 = @mysql_fetch_array($result5))
            {
                if (trim($row12[0])==trim($_SESSION['ahreason2']))
                    echo "<option selected='selected' value=$row12[1]>$row12[0]</option>\n";
                else
                    echo "<option value=$row12[1]>$row12[0]</option>\n";
            }

        echo'
                    <td align="right" height="25" width="100" style="color:black">
                        Begin Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="btaddedhours2">
            ';

            while($row10 = @mysql_fetch_array($timeresult9))
            {
                if ($row10['timeperiod']==$_SESSION['ah2bt'])
                    echo "<option selected='selected' value={$row10['timeperiod']}>
                    {$row10['time']}</option>\n";
                else
                    echo "<option value={$row10['timeperiod']}>{$row10['time']}</option>\n";
            }

        echo'
                    </select>
                    </td>
                    <td align="right" height="25" width="100" style="color:black">
                        End Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="etaddedhours2">
            ';

            while($row11 = @mysql_fetch_array($timeresult10))
            {
                if ($row11['timeperiod']==$_SESSION['ah2et'])
                    echo "<option selected='selected' value={$row11['timeperiod']}>
                    {$row11['time']}</option>\n";
                else
                    echo "<option value={$row11['timeperiod']}>{$row11['time']}</option>\n";
            }

        echo'
                    </select>
                    </td>
                    <td width="225px"></td>
                </tr>';



        if (isset($_SESSION['ahreason3']))
        {
          echo'
                <tr>
                    <td width="225px" align="center"></td>
                    <td align="center" height="25" width="100" style="color:black">
                        Added Hours:
                    </td>
                    <td align="center" height="25" width="200" style="color:black">
                    <select name="reasonaddedhours3" style="width:200px" value=$row12[1]>
              ';

            while($row12 = @mysql_fetch_array($result6))
            {
                if (trim($row12[0])==trim($_SESSION['ahreason3']))
                    echo "<option selected='selected' value=$row12[1]>$row12[0]</option>\n";
                else
                    echo "<option value=$row12[1]>$row12[0]</option>\n";
            }

            echo'
                    <td align="right" height="25" width="100" style="color:black">
                        Begin Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="btaddedhours3">
                ';

            while($row10 = @mysql_fetch_array($timeresult11))
            {
                if ($row10['timeperiod']==$_SESSION['ah3bt'])
                    echo "<option selected='selected' value={$row10['timeperiod']}>
                                {$row10['time']}</option>\n";
                else
                    echo "<option value={$row['timeperiod']}>{$row10['time']}</option>\n";
            }

            echo'
                    </select>
                    </td>
                    <td align="right" height="25" width="100" style="color:black">
                        End Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="etaddedhours3">
                ';

            while($row11 = @mysql_fetch_array($timeresult12))
            {
                if ($row11['timeperiod']==$_SESSION['ah3et'])
                    echo "<option selected='selected' value={$row11['timeperiod']}>
                                {$row11['time']}</option>\n";
                else
                    echo "<option value={$row11['timeperiod']}>{$row11['time']}</option>\n";
            }

            echo'
                    </select>
                    </td>
                    <td width="225px" align="center"></td>
                </tr>
                ';
        }

        else
        {
            echo'
                <tr>
                    <td width="225px" align="center"></td>
                    <td align="center" height="25" width="100" style="color:black">
                        Added Hours:
                    </td>
                    <td align="center" height="25" width="200" style="color:black">
                    <select name="reasonaddedhours3" style="width:200px" value=$row12[1]>
                ';

            while($row12 = @mysql_fetch_array($result6))
            {
                if ($row12[0]=='None')
                    echo "<option selected='selected' value=$row12[1]>$row12[0]</option>\n";
                else
                    echo "<option value=$row12[1]>$row12[0]</option>\n";
            }

            echo'
                    <td align="right" height="25" width="100" style="color:black">
                        Begin Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="btaddedhours3">
                ';

            while($row10 = @mysql_fetch_array($timeresult11))
            {
                echo "<option value={$row10['timeperiod']}>{$row10['time']}</option>\n";
            }

            echo'
                    </select>
                    </td>
                    <td align="right" height="25" width="100" style="color:black">
                        End Time:
                    </td>
                    <td align="center" height="25" width="100" style="color:black">
                    <select name="etaddedhours3">
                ';


            while($row11 = @mysql_fetch_array($timeresult12))
            {
                echo "<option value={$row11['timeperiod']}>{$row11['time']}</option>\n";
            }

            echo'
                    </select>
                    </td>
                    <td width="225px" align="center"></td>
                </tr>
                ';
        }
    }
	   
   else
   {
        echo'
            <tr>
                <td width="225px" align="center"></td>
                <td align="center" height="25" width="100" style="color:black">
                    Added Hours:</td>
		<td align="center" height="25" width="200" style="color:black">
                <select name="reasonaddedhours2" style="width:200px" value=$row12[1]>
    	  '; 

        while($row12 = @mysql_fetch_array($result5))
        {
            if ($row12[0]=='None')
                echo "<option selected='selected' value=$row12[1]>$row12[0]</option>\n";
            else
                echo "<option value=$row12[1]>$row12[0]</option>\n";
        }
    	
        echo'
                <td align="right" height="25" width="100" style="color:black">
                    Begin Time:</td>
		<td align="center" height="25" width="100" style="color:black">
                <select name="btaddedhours2">
            ';
		  
        while($row10 = @mysql_fetch_array($timeresult9))
        {
            echo "<option value={$row10['timeperiod']}>{$row10['time']}</option>\n";
        }
    
        echo'
                </select></td>
                <td align="right" height="25" width="100" style="color:black">
                    End Time:</td>
		<td align="center" height="25" width="100" style="color:black">
                <select name="etaddedhours2">
            ';
          
        while($row11 = @mysql_fetch_array($timeresult10))
        {
            echo "<option value={$row11['timeperiod']}>{$row11['time']}</option>\n";
        }
          
        echo'
                </select>
		</td>
		<td width="225px" align="center"></td>
            </tr>
        </table>
            ';
    }
}

echo'
        <table align="center" width="100%">
            <tr style="height:30px">
            </tr>
            <tr>
            </tr>
            <tr>
                <td align="center"><input type="submit" style="width:175px;
                height:25px; font-size:medium" name="submit" value="Submit Changes" />
	        </td>
            </tr>
        </form>
            <tr>
                <td height="25">
                </td>
            </tr>
        </table>
    </table>
    ';
$_SESSION['changeassignment']=4;
include ('includes/footer.html');  
?>
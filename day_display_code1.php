<?php
function day_display_code1 ($datet)
{

/*
 * VERSION 02_01
 * LAST REVISION 2011-07-27
*/

/*
 * REVISED 2011-06-13 TO BETTER FORMAT THE WIDTH OF THE TABLES SO THEY MATCH WHEN TEXT IS
 * INCREASED IN EXPLORER AND TO MAKE IT MORE UNIFORM.
 *
 * THIS IS THE BEGINNING OF THE DISPLAY PART OF THE PROGRAM
 *
*/

/*
 * REVISED 2011-06-26 TO FORMAT AND EXCISE THE SECONDARY ASSIGNMENT
 * 1.  NEED TO BE ABLE TO HAVE MORE THAN ONE PRIMARY ASSIGNMENT
*/

/*
 * Revised 2011-07-20 - Renamed to day_display_code1.php from daydisplay.php to better show it is
 * a function that comes from the newly developed day_display_code.php which comes from
 * day_display.php SECTIONS 3 & 4
 */
 /*
  * Revised 2011-08-27 to consolidate the table holding the hours display with the colored blocks
  * which show what type of assignment the partner has using the "colspan=4" property on the
  * hour display.
  */
    
/*
 * Revise 2013-04-29 to initialize arrays correctly.
 */

    echo'
        <html>	
        <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>PAAPA Assignment Web</title>
        <link rel="stylesheet" href="style.css" type="text/css"/>
        </head>	
        <div class="content">
        <br>';

/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//These lines of code get the User and the Schedule For MD                                      //
/////////////////////////////////////////////////////////////////////////////////////////////////
 * /
 */
    $test = $_SESSION['initials'];
    $qu = "SELECT first, last 
           FROM mds 
           WHERE initials='$test'";		
		
    $firstlast = mysql_query($qu);
    $frow = @mysql_fetch_row($firstlast);
		
    $formd = $_SESSION['schedmd'];
        
    $fqu = "SELECT first, last 
            FROM mds 
            WHERE initials='$formd'";
    $forfirstlast = mysql_query($fqu);
    $for_row = @mysql_fetch_row($forfirstlast);
		



/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//THIS PART DISPLAYS THE BAR SHOWING USER, SCHEDULE FOR, DATE, "Schedule For Page", "Logout"    //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
    echo'		 	
	<body>
        <center>
        
        <div class="menu">
        <table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#D7DAE1"
        width=95%>
        
            <tr align="center">
            
                <td align="center" width="33%" height="25" style="color:black" 
                bordercolor="#D7DAE1">
		User: '
                .$frow[0].' '.$frow[1].'
                </td>
                    
		<td align="center" width="33%" height="25" style="color:black" 
                bordercolor="#D7DAE1">
		Schedule For: '.
                $for_row[0].
                ' '.
                $for_row[1].'
                </td>
                    
                <td align="center" width="33%" height="21" style="color:black"
                bordercolor="#808080">'.
                $datet->format("D")
                .', '.
                $datet->format("M").
                ' '.
		$datet->format("j").
                ', '.
                $datet->format("Y");
                
    
    
    
    $dayweek1 = $datet->format('l');
    $_SESSION['dai']++;
    $datet->setdate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);
    $dayweek2 = $datet->format('l');
                
    
    echo'
                - '.$datet->format("D").
                ', '.$datet->format("M").
                ' '.$datet->format("j"). 
		', '.$datet->format("Y").
                '</td>'.
           '</tr>'.
       '</table>'.
       '</div>';
    
    
			
    $_SESSION['dai']--;
    $datet->setdate($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai']);
			
/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//Begin and end blocks are obtained from table monthassignment for Primary & Call               //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
    $block3 = "SELECT beginblock, endblock 
               FROM monthassignment 
               WHERE yearnumber={$_SESSION['dty']} 
               AND monthnumber={$_SESSION['dtm']} 
               AND daynumber={$_SESSION['dai']} 
               AND assigntype=1  
               AND mdnumber=(SELECT number 
			     FROM mds 
                             WHERE initials='{$_SESSION['schedmd']}')";
    $block2 = mysql_query($block3);
    $numblock = mysql_num_rows($block2);
    $block = mysql_fetch_row($block2);
   
  
   
    $hblock3 = "SELECT beginblock, endblock 
                FROM monthassignment 
                WHERE yearnumber={$_SESSION['dty']} 
                AND monthnumber={$_SESSION['dtm']} 
                AND daynumber={$_SESSION['dai']} 
                AND assigntype=3 
                AND mdnumber=(SELECT number 
			      FROM mds 
                              WHERE initials='{$_SESSION['schedmd']}' 
                              ORDER BY beginblock)";
    $hblock2 = mysql_query($hblock3);
    $hblock = @mysql_fetch_row($hblock2);

    echo'
        <br><br>';
        /*
        <form method="post" action="menuResults.php">
        <table align="center" width=95%>        
            <tr align="center">
                <td>
                    <input type="submit" name="Me2" value="Schedule For Page" class="btn">
                </td>
                
                <td>
                    <input type="submit" name="Me2" 
				        value="Complete Month" class="btn">
                </td>
                
                <td>
                    <input type="submit" name="Me2" 
				        value="Help" class="btn">
                </td>
                
                <td>
                    <input type="submit" name="Me2" 
				        value="Logout" class="btn">
                </td>
            </tr>
        </form>
        </table>
         * 
         */
    
        require_once 'menuBar.php';
        menuBar(1567);
	
        echo'
	<br>
        <br>
        <br>

	<div class="table">
	<table align="center" border="1" bordercolor="#808080" width=90%>
        
            <tr bgcolor="#505050">
            
                <td align="center" width="6%" height="25" bordercolor="#808080" bgcolor="blue">
                    <b>
                        OFF
                    </b>
                </td>';	
			
    if (isset($_SESSION['dayassignment1'])) {
        echo'
                <td align="center" width="17%" bordercolor="#808080" bgcolor="green" height="25" 
		style="color:white">
                    <b>
                        Day Assignment: '.
                        $_SESSION['dayassignment1'].'
                    </b>
                </td>';
    }
    else {    
        echo'
                <td align="center" width="17%" bordercolor="#808080" bgcolor="green" height="25" 
		style="color:white">
                    <b>
                        Day Assignment: NONE
                    </b>
                </td>';
    }
    if (!isset($_SESSION['callassignment1']))
        echo '  <td align="center" width="17%" bordercolor="#808080" bgcolor="yellow" height="25">
                    <b>
                        Call: NONE 
                    </b>
                </td>';
    else
	echo '  <td align="center" width="17%" bordercolor="#808080" bgcolor="yellow" height="25">
                    <b>
                        Call: '.
                        $_SESSION['callassignment1'].'
                    </b>
                </td>';
			
        echo'   
		<td align="center" width="6%" height="25" bordercolor="#808080" bgcolor="red">
                    <b>
                        Added Hours
                    </b>
                </td>
                
            </tr>
            
        </table>';

		

/*		
//////////////////////////////////////////////////////////////////////////////////////////////////
//THIS PART DISPLAYS THE DAY BAR SHOWING THE DAY OF THE WEEK UP TO MIDNIGHT AND THE NEXT DAY    //
//TO 07:00                                                                                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */	
    echo'
        <br>	
        <table align="center" width=100% 
            style="border-width:1px; border-style:solid; border-color:#808080;">

            <tr bordercolor="#808080">

                <td align="center" width=72% 
                    style="border-width:1px; border-style:solid;border-color:#808080;">'.
                    $dayweek1.'
                </td>

                <td align="center" width=28% 
                    style="border-width:1px; border-style:solid;border-color:#808080;">'.
                    $dayweek2.'
                </td>
            </tr>

        </table>
	';
		
		
/*		
//////////////////////////////////////////////////////////////////////////////////////////////////
//Go to FUNCTION day_display_code2() to display the time intervals on the color graph           //
//(stripe) of assignments                                                                       //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
    day_display_code2();


		
		
		
		
/*			
//////////////////////////////////////////////////////////////////////////////////////////////////
//Make a matrix of $stripe (color stripes) and initialize all of the color stripes to 0 (OFF)   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
    $stripe = array();
    for ($x = 0; $x < 100; $x++)
    {
        $stripe[$x]=0;
    }

/*
 * Change the value of the $stripe matrix to 3 where a CALL assignment is present
 */
    if (isset($_SESSION['callassignment1']))
    {
        for ($index=$_SESSION['callassignment1beginblock'];
             $index<$_SESSION['callassignment1endblock'];
             $index++)
        {
            $stripe[$index]=3;
        }
    }

/*
 * If there is a second call assignment make the $stripe matrix=3 for that assignment
 */
    
    if (isset($_SESSION['callassignment2']))
    {
        for ($index=$_SESSION['callassignment2beginblock'];
             $index<$_SESSION['callassignment2endblock'];
             $index++)
        {
            $stripe[$index]=3;
        } 
    }
		
	
    
/*
 * Check to see if the PRIMARY assignment is a WORK assignment and not a Vac day or day after call
 * If so, make those $stripe matrix values=1
 */

    if (isset($_SESSION['dayassignment1'])) {
    $q = "SELECT type 
          FROM assignments 
          WHERE assignment = '{$_SESSION['dayassignment1']}'";
    $q1 = mysql_query ($q);
    while ($q2 = @mysql_fetch_row($q1))
    {
        if (trim($q2[0])=='WEEKDAY')
        {
            for ($index=$block[0]; $index<$block[1]; $index++)
            {
                $stripe[$index]=1;
            }
            if ($numblock>1)
            {
                $block = mysql_fetch_row($block2);
                for ($index=$block[0]; $index<$block[1]; $index++)
                {
                    $stripe[$index]=1;
                }
            }
        }
    }
    }

    if (isset($_SESSION['dayassignment2']))
    {
        $q = "  SELECT type
                FROM assignments
                WHERE assignment = '{$_SESSION['dayassignment2']}'
                AND beginblock < 5 AND endblock > 5";
        $q1 = mysql_query ($q);
        while ($q2 = @mysql_fetch_row($q1))
        {
            if (trim($q2[0])=='WEEKDAY')
            {
                for ($index=$block[0]; $index<$block[1]; $index++)
                {
                    $stripe[$index]=1;
                }
            }
        }
    }

    
/*
 * Get the added hours and assign the $stripe matrix=4
 */    
    $ablock3 = "SELECT beginblock, endblock 
		FROM monthassignment 
		WHERE yearnumber={$_SESSION['dty']} 
		AND monthnumber={$_SESSION['dtm']} 
		AND daynumber={$_SESSION['dai']} 
		AND assigntype=4 
		AND mdnumber=(SELECT number 
                              FROM mds 
                              WHERE initials='{$_SESSION['schedmd']}')";
                              
    $ablock2 = mysql_query($ablock3);
    while ($ablock = @mysql_fetch_row($ablock2))
    {
        for ($index=$ablock[0]; $index<$ablock[1]; $index++)
        {
            $stripe[$index]=4;
        }
    }
		
    echo'


            <tr> ';


/*
//////////////////////////////////////////////////////////////////////////////////////////////////
//If primary assignment ($stripe[$index]==1) is an OFF assignment then make the color of those	//
//stripes blue; if it is a WORK assignment make the stripes green.                              //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * Make ADDED HOURS red
 * Make CALL assignments yellow
 */
		
    for ($index=0; $index<100; $index++)
    {
        if ($stripe[$index]==0)
        {
            echo '  <td height="30" width=1% bgcolor="blue" style="font-size:8; color:blue">
                        O
                    </td>';
        }
        else if ($stripe[$index]==4)
        {
            echo '  <td height="30" width=1% bgcolor="red" style="font-size:8; color:red">
                        H
                    </td>';
        }
        else if ($stripe[$index]==3)
        {
            echo'<td height="30" width=1% bgcolor="yellow" style="font-size:8; color:yellow">
                        C
                    </td>';
        }
        else if ($stripe[$index]==1)
        {
            echo ' <td height="30" width=1% bgcolor="green" style="font-size:8; color:green">
                        P
                    </td>';
        }
    }
		
		
    echo'
            </tr>
        </table>
        <br>
        </body>
        </center>
        </html>
        <br>
        <br>
        ';
}
?>

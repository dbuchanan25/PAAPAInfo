<?php
function daydisplay ($datet)
{
/*
 * Version 02_01
 */
/*
//LAST REVISION 2011-06-26
//REVISED 2011-06-13 TO BETTER FORMAT THE WIDTH OF THE TABLES SO THEY MATCH WHEN TEXT IS 
//INCREASED IN EXPLORER AND TO MAKE IT MORE UNIFORM.
//////////////////////////////////////////////////////////////////////////////////////////////////
//THIS IS THE BEGINNING OF THE DISPLAY PART OF THE PROGRAM                                      //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
 /*
  * REVISED 2011-06-26 TO FORMAT AND EXCISE THE SECONDARY ASSIGNMENT
  * 1.  NEED TO BE ABLE TO HAVE MORE THAN ONE PRIMARY ASSIGNMENT
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
    
$mdentryStatement = "INSERT INTO mdlog
                    VALUES ('{$_SESSION['schedmd']}','', 'Viewing daydisplay.php', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);

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
        <table align="center" class="menu" border="1" bordercolor="#D7DAE1" bgcolor="#E5E5E5"
        width=90%>
        
            <tr align="center">
            
                <td align="center" width="18%" height="25" style="color:black" 
                bordercolor="#D7DAE1">
		User: '
                .$frow[0].' '.$frow[1].'
                </td>
                    
		<td align="center" width="18%" height="25" style="color:black" 
                bordercolor="#D7DAE1">
		Schedule For: '.
                $for_row[0].
                ' '.
                $for_row[1].'
                </td>
                    
                <td align="center" width="24%" height="21" style="color:black"
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
                '</td>';
    
    
			
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
    $block = @mysql_fetch_row($block2);
   
  
   
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
                <td align="center" width="12%" height="25px" bordercolor="#D7DAE1">
                    <a href="choose.php">
                        Schedule For Page
                    </a>
                </td>
                
                <td align="center" width="12%" height="25px" bordercolor="#D7DAE1">
                    <a href="monthcalendar2.php">
                        Complete Month
                    </a>
                </td>
                
                <td align="center" width="9%" height="25px" bordercolor="#D7DAE1">
                    <a href="logout.php">
                        Logout
                    </a>
                </td>
            </tr>
        </table>
        </div>
			
	<br>

	<div class="table">
	<table align="center" border="1" bordercolor="#808080" width=90%>
        
            <tr bgcolor="#505050">
            
                <td align="center" width="6%" height="25" bordercolor="#808080" bgcolor="blue">
                    <b>
                        OFF
                    </b>
                </td>		
			
                <td align="center" width="17%" bordercolor="#808080" bgcolor="green" height="25" 
		style="color:white">
                    <b>
                        Day Assignment: '.
                        $_SESSION['primaryassignment'].'
                    </b>
                </td>';
    
    if (empty($_SESSION['callassignment1']))
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
        <table align="center" width=100% border="1" bordercolor="#808080">

            <tr bordercolor="#808080">

                <td align="center" width=72% bordercolor="#808080">'.
                    $dayweek1.'
                </td>

                <td align="center" width=28% bordercolor="#808080">'.
                    $dayweek2.'
                </td>

            </tr>

        </table>
	';
		
		
/*		
//////////////////////////////////////////////////////////////////////////////////////////////////
//Go to FUNCTION timedisplay() to display the time intervals on the color graph                 //
//(stripe) of assignments                                                                       //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */
    timedisplay();


		
		
		
		
/*			
//////////////////////////////////////////////////////////////////////////////////////////////////
//Make a matrix of $stripe (color stripes) and initialize all of the color stripes to 0 (OFF)   //
//////////////////////////////////////////////////////////////////////////////////////////////////
 * 
 */	
    $stripe[100];
    for ($index=0; $index<100; $index++)
    {
        $stripe[$index]=0;
    }


    
/*
 * Change the value of the $stripe matrix to 3 where a CALL assignment is present
 */
    for ($index=$hblock[0]; $index<$hblock[1]; $index++)
    {
        $stripe[$index]=3;
    }

/*
 * If there is a second call assignment make the $stripe matrix=3 for that assignment
 */
    $hblock = @mysql_fetch_row($hblock2);
    
    if (!empty($hblock))
    {
        for ($index=$hblock[0]; $index<$hblock[1]; $index++)
        {
            $stripe[$index]=3;
        } 
    }
		
	
    
/*
 * Check to see if the PRIMARY assignment is a WORK assignment and not a Vac day or day after call
 * If so, make those $stripe matrix values=1
 */

    $q = "SELECT type 
          FROM assignments 
          WHERE assignment = '{$_SESSION['primaryassignment']}'";
    $q1 = mysql_query ($q);
    $q2 = @mysql_fetch_row($q1);

    if (trim($q2[0])=='WEEKDAY')
    {
        for ($index=$block[0]; $index<$block[1]; $index++)
        {
            $stripe[$index]=1;
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
        <table align="center" width=100% style="background-color:000000">

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
            echo '  <td height="30" width=1% bgcolor="blue" style="font-size:8;color:blue">
                        O
                    </td>';
        }
        else if ($stripe[$index]==4)
        {
            echo '  <td height="30" width=1% bgcolor="red" style="font-size:8;color:red">
                        H
                    </td>';
        }
        else if ($stripe[$index]==3)
        {
            echo '  <td height="30" width=1% bgcolor="yellow" style="font-size:8;color:yellow">
                        C
                    </td>';
        }
        else if ($stripe[$index]==1)
        {
            echo '  <td height="30" width=1% bgcolor="green" style="font-size:8;color:green">
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

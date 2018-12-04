<?php
session_start();
    
require_once ('connect2.php');

if (!isset($_SESSION['initials']))
{
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}

else
{
    include ('includes/header.php');
    require_once ($_SESSION['login2string']);

   
    echo'
    <link rel="stylesheet" href="style2.css" type="text/css">
    ';
    
    echo '<title>ORMGR Assignments</title>';
    
    /*
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'ormpage.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
     * 
     */

    echo'
        <body><center>';
    
    
    include_once 'menuBar3.php';
    menuBar3(1663);
    
    
    
    
    echo '
        <br><br>
	<center><zz>OR Manager Assignments</zz><br>
        <font size="4">Pick the appropriate day.</font><br><br>';

	$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
        $datetime->modify("+1 day");
        
    echo'    
            <form method="post" action="ormassignments.php" class="input">
            <table>
                <tr>
                    <td style="width:33%; border:none;"><b>Month</b></td><td style="width:33%; border:none;">
                    <b>Day</b></td><td style="width:33%; border:none;"><b>Year</b></td>
                </tr>
                

                <tr>
                    <td style="width:33%; border:none;">
                    
                    <select name="month">';
                    if ($datetime->format('n')=="1")
                    {
                        echo '<option selected value=1>January</option>';
                    }
                    else 
                    {
                        echo '<option value=1>January</option>';
                    }
                    if ($datetime->format('n')=="2")
                    {
                        echo '<option selected value=2>February</option>';
                    }
                    else 
                    {
                        echo '<option value=2>February</option>';
                    } 
                    if ($datetime->format('n')=="3")
                    {
                        echo '<option selected value=3>March</option>';
                    }
                    else 
                    {
                        echo '<option value=3>March</option>';
                    }
                    if ($datetime->format('n')=="4")
                    {
                        echo '<option selected value=4>April</option>';
                    }
                    else 
                    {
                        echo '<option value=4>April</option>';
                    }
                    if ($datetime->format('n')=="5")
                    {
                        echo '<option selected value=5>May</option>';
                    }
                    else 
                    {
                        echo '<option value=5>May</option>';
                    }
                    if ($datetime->format('n')=="6")
                    {
                        echo '<option selected value=6>June</option>';
                    }
                    else 
                    {
                        echo '<option value=6>June</option>';
                    }
                    if ($datetime->format('n')=="7")
                    {
                        echo '<option selected value=7>July</option>';
                    }
                    else 
                    {
                        echo '<option value=7>July</option>';
                    }
                    if ($datetime->format('n')=="8")
                    {
                        echo '<option selected value=8>August</option>';
                    }
                    else 
                    {
                        echo '<option value=8>August</option>';
                    }
                    if ($datetime->format('n')=="9")
                    {
                        echo '<option selected value=9>September</option>';
                    }
                    else 
                    {
                        echo '<option value=9>September</option>';
                    }
                    if ($datetime->format('n')=="10")
                    {
                        echo '<option selected value=10>October</option>';
                    }
                    else 
                    {
                        echo '<option value=10>October</option>';
                    }
                    if ($datetime->format('n')=="11")
                    {
                        echo '<option selected value=11>November</option>';
                    }
                    else 
                    {
                        echo '<option value=11>November</option>';
                    }
                    if ($datetime->format('n')=="12")
                    {
                        echo '<option selected value=12>December</option>';
                    }
                    else 
                    {
                        echo '<option value=12>December</option>';
                    }
    
    echo'                                               
                    </select>
                    </td>
                    <td style="width:33%; border:none;">
                    <select name="day">';

    
    
                for ($x=1; $x<=31; $x++)
                {
                    if ($x == $datetime->format('j'))
                        echo'<option selected value='.$x.'>'.$x.'</option>';
                    else 
                        echo'<option value='.$x.'>'.$x.'</option>';
                }

                echo'
                    </select>
                    </td>
                    <td style="width:33%; border:none;">
                    <select name="year">';

    
    
                for ($x=2016; $x<=2020; $x++)
                {
                    if ($x == $datetime->format('Y'))
                        echo'<option selected value='.$x.'>'.$x.'</option>';
                    else 
                        echo'<option value='.$x.'>'.$x.'</option>';
                }

                echo'
                    </select>
                    </td>
                </tr>
            </table>';

    echo'
            </tr>
        </table>
        <br><br><br>
        
        <table align="center" width="100%" style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px; border:none">
            <tr>
                <td width="50%" align="center" style="border: none">
                     <input type="submit" name="ORMA" value="SUBMIT" class="btn">
                </td>
                </form>
            </tr>
        </table>';
}


?>

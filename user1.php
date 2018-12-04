<?php

/*
 * Version 01_01
 * Page to determine access of the user and send them to the correct user page.
 *
 * Last Revised:  2015-07-03
 */
 
session_start();
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="styleP.css" type="text/css">
';
echo '<TITLE>User Actions</TITLE>';

/*
 * Check to see if the user is logged in.
 * If not send them to the login page.
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
    include ('includes/header.php');

    
    if (isset($_POST['user_action'])) {         
        if ($_POST['user_action']=='delete') {
            $q = "SELECT * FROM mds ORDER BY last, first";
            $r = mysql_query($q);
            
    echo '<form method="post" action="deleteUser.php">
          <div class="content">
          <table align="center" width=100%  bgcolor="#E5E5E5">
                <tr>
                    <td width=40% align="right"> Choose User to Delete: 
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">
                    <select name="usernumber">';
            while($row = mysql_fetch_array($r)) {   
                echo "<option value={$row['number']}>{$row['last']}, {$row['first']}</option>\n";
            }
    echo'        </select>
                    </td>
                    <td width=55%>
                    </td>
                </tr>
            </table>
            <br><br>';
    }
    
    
    
    else if ($_POST['user_action']=='add') {
        echo   '<form method="post" action="addUser.php">
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>First Name:</b> 
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="firstName" size="50" 
                        value="enter first name here" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Last Name:</b> 
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="lastName" size="50" 
                        value="enter last name here" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Initials:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="initials" size="50" 
                        value="enter initials here" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Number:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="number" size="50" 
                        value="enter partner number here" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>                
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Peds Status:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="peds" size="50" 
                        value="enter 1 for a peds partner, 0 otherwise" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Administrative Days:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="administrative" size="50" 
                        value="enter number of administrative days for this partner" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Business Days:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="business" size="50" 
                        value="enter number of business days for this partner" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>
                <div class="table">
                <table align="center" width=100%>
                    <tr>
                        <td width=40% align="right"> <b>Pay Fraction:</b>
                        </td>
                        <td width=10%>
                        </td>
                        <td width=50% align="left">
                        <input type="text" NAME="payfraction" size="50" 
                        value="enter pay fraction for this partner" >
                        </td>
                    </tr>
                </table>
                </div>
                <br>    
                <br>
                <br>';
    }

    else if ($_POST['user_action']=='modify') {

        $q = "SELECT * FROM mds ORDER BY last, first";
        $r = mysql_query($q);
        
        echo'<form method="post" action="modifyUser.php">
            <div class="content">
            <table align="center" width=100%  bgcolor="#E5E5E5">
                <tr>
                    <td width=40% align="right"> Choose User to Modify: 
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">
                    <select name="number">';
            while($row = mysql_fetch_array($r))
            {   
                echo "<option value={$row['number']}>{$row['last']}, {$row['first']}</option>\n";
            }
    echo'        </select>
                    </td>
                    <td width=55%>
                    </td>
                </tr>
            </table>
            <br><br>';
    }

    echo '<table align="center" 
            width=100%>
            <tr>
            </tr>
            <tr>
                <td align="center">
                <input type="submit" name="submit" class="btn" 
                    value="SUBMIT">
                </td>
            </tr>
        </table>
        </form>
        <br>
        <br>
        ';

        include ('includes/startover.html');
        include ('includes/logoutDirect.php');
    }
    
    else {
        include('choose1_02_01.php');
    }
}
?>

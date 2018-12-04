<?php
/*
 * Version 01_01
 *
 * Last Revised:  2015-07-03
 */
 
session_start();
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="styleP.css" type="text/css">
';
echo '<TITLE>Modify Partner</TITLE>';




/*
 * Check to see if the user is logged in.
 * If not send them to the login page.
 */
if (!isset($_SESSION['initials'])) {
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}
else {
    include ('includes/header.php');
    $q = "SELECT * FROM mds WHERE number = {$_POST['number']}";
    $r = mysql_query($q);
    $a = mysql_fetch_array($r);
    
    $_SESSION['usernumber'] = $_POST['number'];
    
    echo '<center><h3>
          Make any necessary changes and click on the SUBMIT button.
          </center></h3><br><br>';
    
    echo '<div class="table">
            <table align="center" width=100%>
                <tr>
                    <td width=40% align="right"> <b>
                    (cannot be changed) Number:</b> 
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">'.
                    $a['number'].'
                    </td>
                </tr>
            </table>
            </div>
            <br>';
    
    echo '<div class="table">
            <table align="center" width=100%>
                <tr>
                    <td width=40% align="right"> <b>
                    (cannot be changed) Initials:</b> 
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">'.
                    $a['initials'].'
                    </td>
                </tr>
            </table>
            </div>
            <br>';

    echo'<form method="post" action="modifyUser2.php">
            <div class="table">
            <table align="center" width=100%>
                <tr>
                    <td width=40% align="right"> <b>First Name:</b> 
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">
                    <input type="text" NAME="firstName" size="50" value="'.$a['first'].'" >
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
                    <input type="text" NAME="lastName" size="50" value="'.$a['last'].'" >
                    </td>
                </tr>
            </table>
            </div>
            <br>
            <div class="table">
            <table align="center" width=100%>
                <tr>
                    <td width=40% align="right"> <b>Access Code:</b>
                    </td>
                    <td width=10%>
                    </td>
                    <td width=50% align="left">
                    <input type="text" NAME="access" size="50" value="'.$a['access'].'" >
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
                    <input type="text" NAME="peds" size="50" value="'.$a['peds'].'" >
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
                    <input type="text" NAME="administrative" size="50" value="'.$a['admin'].'" >
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
                    <input type="text" NAME="business" size="50" value="'.$a['business'].'" >
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
                    <input type="text" NAME="payfraction" size="50" value="'.$a['payfraction'].'" >
                    </td>
                </tr>
            </table>
            </div>
            <br>            
            <br>
            <br>';
    
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
?>

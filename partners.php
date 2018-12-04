<?php

session_start();
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="styleP.css" type="text/css">
';

/*
 * Version 01_01
 * Page to allow changes to pay percentage, access, etc. for partners
 */
/*
 * Last Revised: 2015-07-03 
 */

if (!isset($_SESSION['initials'])) {
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
} 
else {
    include ('includes/header.php');

    echo '<title>Partner Details</title>';

    $checkCredS = "SELECT access FROM mds WHERE initials LIKE " .
            "'{$_SESSION['initials']}'";
    $checkCredQ = mysql_query($checkCredS);
    if ($checkCredQ) {
        $checkCred = mysql_fetch_array($checkCredQ);
        $cred = $checkCred['access'];
        if ($cred != 1) {
            $_SESSION = array();
            session_destroy();
            setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0);
            require_once ('includes/login_functions.inc.php');
            $url = absolute_url();
            header("Location: $url");
            exit();
        }
        else {
           echo '<br><br>
                    <form method="post" action="user1.php" class="input">';
            echo '<table width="100%" bgcolor="#E5E5E5">
                    <tr>
                    <th align="center" width=45%>Choose User Action:</th>
                        <td align="left" width=55%>';
            echo'
                        <input name="user_action" type="radio" 
                            value="add">
                        Add Partner<br>';
            echo'
                        <input name="user_action" type="radio" 
                            value="delete">
                        Delete Partner<br>';
            echo'
                        <input name="user_action" type="radio" 
                            value="modify">
                        Modify Partner<br>';
            echo '
                            </td>
                        </tr>
                    </table>
                    <br><br>
                    <table align="center" width="100%">
                        <tr>
                        </tr>
                        <tr>
                            <td align="center">
                            <input type="submit" name="submit" class="btn" 
                            value="SUBMIT">
                            </td>
                        </tr>
                        </form>
                    </table>
                    <br><br>
                ';
            include ('includes/startover.html');
            include ('includes/logoutDirect.php');
        }
    }
}
?>

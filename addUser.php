<?php
if (!isset($_SESSION)) { session_start(); }

/*
 * Version 01_01
 * Page to determine access of the user and send them to the correct user page.
 *
 * Last Revised:  2015-07-03
 */
 

require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="styleP.css" type="text/css">
';
echo '<TITLE>Add User</TITLE>';




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
    $unique = true;
    $q = "SELECT number FROM mds";
    $r = mysql_query($q);
    while($row = mysql_fetch_array($r))
    {
        if ($row['number'] == $_POST['number'])
        {
            $unique = false;
        }
    }
    if (
        strstr($_POST['firstName'],'enter first name here')==false
        &&  
        strstr($_POST['lastName'],'enter last name here')==false
        &&
        strstr($_POST['number'],'enter partner number here')==false
        &&
        strstr($_POST['peds'],'enter 1 for a peds partner, 0 otherwise')==false
        && 
        ($unique==true)
        &&
        strstr($_POST['administrative'], 'enter number of administrative days for this partner')==false
        &&
        strstr($_POST['business'], 'enter number of business days for this partner')==false
        &&
        strstr($_POST['payfraction'], 'enter pay fraction for this partner')==false
        )
      {
            $q = "INSERT INTO mds ".
                 "VALUES ".
                 "( ".
                 "'{$_POST['lastName']}', ".
                 "'{$_POST['firstName']}', ".
                 "'{$_POST['number']}', ".
                 "0, ".
                 "{$_POST['peds']}, ".
                 "{$_POST['administrative']}, ".
                 "{$_POST['business']}, ".
                 "{$_POST['payfraction']}, ".
                 "'{$_POST['initials']}', ".
                 "sha1('{$_POST['number']}') ".
                 ")";
            if(mysql_query($q))
            {
                echo '<center><h2>'.
                      $_POST['firstName'].' '.$_POST['lastName'].
                      ' has been entered into the User database.
                      </center></h2><br><br>';
            }
            else
            {
                echo '<center><h2>The user was not entered correctly.
                      </h2></center>';
            }
      }
      else if ($unique==false)
      {
          echo '<center><h2>That number is already being used.<br>
                Please try again.</h2></center><br><br>';
      }
      else
      {
          echo '<center><h2>There was missing or incomplete data.<br>
                Please try again.</h2></center><br><br>';
      }
    include ('includes/startover.html');
    include ('includes/logoutDirect.php');
}
?>

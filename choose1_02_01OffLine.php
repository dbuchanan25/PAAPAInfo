<?php 
session_start();

echo session_id();
/*
 * VERSION 02_01
 */
/*
 * Last Revised 2011-08-07
 */
                             


echo'
<link rel="stylesheet" href="styleP.css" type="text/css">
';


if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}
else
{
   $_SESSION['loginstring']='connect.php';
   $_SESSION['login2string']='connect2.php';
   
   $page_title = 'Choosing Page';

   require_once ($_SESSION['login2string']);

   include ('includes/header.php');
   $sqlpass = " SELECT number, access
                FROM mds
                WHERE initials='{$_SESSION['initials']}'";
   $sqlpassq = mysql_query($sqlpass);
   
   $sqlpassr = @mysql_fetch_row($sqlpassq);
      
   if ($sqlpassr) {
    $_SESSION['access'] = $sqlpassr[1];
   }

   $sqlpass2 = "SELECT last, access
                FROM mds
                WHERE initials='{$_SESSION['initials']}'
                AND pass=SHA1('$sqlpassr[0]')";
                

   $sqlpass2q = mysql_query($sqlpass2);
   $sqlpassr2 = mysql_fetch_row($sqlpass2q);
   
   
   if (isset($sqlpassr2[0]))
   {
      if (isset($_POST['submitted']))
      {
	     $errors=array();
		 if (!empty($_POST['pass1']))
		 {
		    if ($_POST['pass1']!=$_POST['pass2'])
			{
			   $errors[] = 'Your new password did not match.';
			}
			else
			{
			   $np = trim($_POST['pass1']);
			}
		 }
		 else
		 {
		    $errors[] = 'You forgot to enter your new password.';
		 }
		 
		 if (empty($errors))
		 {
		    echo '  <form action="choose.php" method="post">';
		    $q = "  UPDATE mds
                            SET pass=SHA1('$np')
                            WHERE initials='{$_SESSION['initials']}'";
			$r = mysql_query($q);
			echo '  <h2 align="center">
                                    You have successfully changed your password.
                                <br>
                                </h2>';
			echo '  <p align="center">
                                <br>
                                <input type="submit" name="submit" value="Continue" />
                                </p>
                            </form>';
		 }
		 else
		 {
		    echo '  <form action="choose.php" method="post">';
		    echo '  <h1>
                            <center>
                                Error!
                            </center>
                            </h1>';
                    echo '  <p>
                            <center>
                                The following errors occurred:
                            <br />';
                    echo " - $errors[0]<br />\n";
                    echo '  </p>
                            <p>
                                Please try again.
                            </p>
                            <p>
                            <br />
                            </p>';
                    echo '  <p align="center">
                            <br>
                            <input type="submit" name="submit" value="Continue" />
                            </p>
                            </form>';
		 }
      }
	  else
	  {
	  	echo '<h1><center>Change Your Password</h1><br>';
		echo '<h2><center>Your current password is your anesthesiologist number.<br>
                          Please change to a more secure password.<br>
			  The new password may be up to 20 characters long.</h2>';
		echo '  <br><form action="choose.php" method="post">
                        <p><center>New Password: <input type="password" name="pass1" size="10"
			  maxlength="20" /> </p><br><br>
                        <p>
                        <center>
                            Confirm New Password:
                        <input type="password" name="pass2" size="10" maxlength="20" />
                        </center>
                        </p>
                        <br>
                        <br>
			<p>
                        <center>
                        <input type="submit" name="submit" value="Change Password" />
                        </p>
			<input type="hidden" name="submitted" value="TRUE" />
			</form>
                        <p>
                        <br>
                        </p>';
      }
   }
   else
   { 
                   echo'
                    <link rel="stylesheet" href="styleP.css" type="text/css">
                    ';
       
	   	   echo '   <center>
                            <h1>
                                The website is currently being updated.<br>You may try again in a few minutes.<br><br>
                                In the meantime, take this opportunity to review the path of the next total solar eclipse,<br>
                                a rare event that is spectacular to view in person.<br>
                                Thanks for your patience.
                            </h1>
                            </center>
                            <br>
                            <br>';
		
		  
                   
                   
                   echo'
                         </form>
		         </table>';	   
		   echo '<br><br><br><br>';
		   
		   echo '<br><br><br><br>';
		   echo '   <h2>
                            <center>
                                Today is: '.date("l, Y F d").'
                            </center>
                            </h2>
                            <br>
                            <br>';
		   $seday = mktime(15,22,57,7,2,2019);
                   //echo $seday.'<br>';
		   $todayday = mktime(date("H"), date("i"), date("s"), date("m"), 
		               date("d"), date("Y"));
                   $todayday += 7200;
                   //echo strftime("%H:%M:%S", $todayday);
		   $secondsdiff = $seday - $todayday;
                   //echo $secondsdiff.'<br>';
		   $daysdiff = floor($secondsdiff/86400);
		   $secondsleft1 = $secondsdiff-($daysdiff*86400);
		   $hoursdiff = floor($secondsleft1/3600);
		   $secondsleft2 = $secondsleft1-($hoursdiff*3600);
		   $minutesdiff = floor($secondsleft2/60);
		   echo '   <h2>
                            <center>
                                Time until the next total solar eclipse - Tuesday, 2019 July 2 is: '
                                .$daysdiff.' days, '.$hoursdiff.' hours, and '.
                                $minutesdiff.' minutes.
                            <br>
                            </center>
                            </h2>';
		   echo '   <br><br>
                            <h3>
                            <center>
                                For more information go to:
                                <br><h2>
            <a href="http://xjubier.free.fr/en/site_pages/solar_eclipses/TSE_2019_GoogleMapFull.html">
                <font color="red">
                2019 Total Solar Eclipse
            </a>';
   }
}
?>
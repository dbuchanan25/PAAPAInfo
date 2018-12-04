<?php

if (!isset($_SESSION)) { session_start(); }

echo'
<link rel="stylesheet" href="style.css" type="text/css">
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
   $page_title = 'Choosing Page';
   require_once ('connect2.php');
   include ('includes/header.php');
   $sqlpass = "Select number from mds where initials='{$_SESSION['initials']}'";
   $sqlpassq = mysql_query($sqlpass);
   $sqlpassr = mysql_fetch_row($sqlpassq);
   $sqlpass2 = "Select last from mds where initials='{$_SESSION['initials']}' and pass=SHA1('$sqlpassr[0]')";

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
		    echo '<form action="choose.php" method="post">';
		    $q = "UPDATE mds SET pass=SHA1('$np') WHERE initials='{$_SESSION['initials']}'";
			$r = mysql_query($q);
			echo '<h2 align="center">You have successfully changed your password.<br></h2>';
			echo '<p align="center"><br><input type="submit" name="submit" value="Continue" /></p></form>';
		 }
		 else
		 {
		    echo '<form action="choose.php" method="post">';
		    echo '<h1><center>Error!</h1>';
			echo '<p><center>The following errors occurred: <br />';
			   echo " - $errors[0]<br />\n";
			echo '</p><p>Please try again.</p><p><br /></p>';
			echo '<p align="center"><br><input type="submit" name="submit" value="Continue" /></p></form>';
		 }
      }
	  else
	  {
	  	echo '<h1><center>Change Your Password</h1><br>';
		echo '<h2><center>Your current password is your anesthesiologist number.<br>
		      Please change to a more secure password.<br>
			  The new password may be up to 20 characters long.</h2>';
		echo '<br><form action="choose.php" method="post">
		      <p><center>New Password: <input type="password" name="pass1" size="10"
			  maxlength="20" /> </p><br><br>
			  <p><center>Confirm New Password: <input type="password" name="pass2" size="10"
			  maxlength="20" /> </p><br><br>
			  <p><center><input type="submit" name="submit" value="Change Password" /></p>
			  <input type="hidden" name="submitted" value="TRUE" />
			  </form><p><br></p>';
      }
   }
   else
   {   
	   	   echo '<center><h2>Choose either PAMS or Patient Satisfaction Survey</center></h2><br><br>';
		
		   echo '<table align="center" width="100%">
		         <form method="post" action="choose2BB.php" class="input">
		         <tr>
		         <td align="center">
				 <input type="submit" name="PAMS" value="PAMS" class="btn">
				 </td>
				 </tr>
				 <tr>
				 <td height="25">
				 </td>
				 </tr>
		         </table>';	   
		   echo '<br><br><br><br>';
		   
		   
		   echo '<h3><center>Today is: '.date("l, Y F d").'</center></h3><br><br>';
		   $seday = mktime(12,38,33,8,17,2017,1);
		   $todayday = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"), 0);
		   $secondsdiff = $seday - $todayday;
		   $daysdiff = floor($secondsdiff/86400);
		   $secondsleft1 = $secondsdiff-($daysdiff*86400);
		   $hoursdiff = floor($secondsleft1/3600);
		   $secondsleft2 = $secondsleft1-($hoursdiff*3600);
		   $minutesdiff = floor($secondsleft2/60);
		   echo '<h3><center>Time until the total solar eclipse of Monday, 2017 August 21 is: '
		         .$daysdiff.' days, '.$hoursdiff.' hours, and '.$minutesdiff.' minutes.<br></center></h3>';
		   echo '<h3><center>For more information go to <a href="http://eclipse.gsfc.nasa.gov/SEgoogle/SEgoogle2001/SE2017Aug21Tgoogle.html"><font color="blue">NASA 2017 Solar Eclipse.</a>';
	   
   }
}
?>
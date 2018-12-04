<?php
session_start();

$page_title = 'Login';
include ('includes/header.php');

if (!empty($errors))
{
   echo '<h1>Error!</h1> <p class="error">The following error(s) occurred:<br />';
   foreach ($errors as $msg)
   {
       echo " - $msg<br />\n";
   }
   echo '</p><br><br.<p>Please try again.</p><br><br>';
}
?>

<h1>Login</h1>
<form action="login.php" method="post">
<p align="center">ID:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" 
name="initials" size="18"  maxlength="20" /></p>
<p align="center">Password: <input type="password" name="pass" size="20" maxlength="25" /></p>
<br></br>
<p align="center"><input type="submit" name="submit" value="Login" /></p>
<input type="hidden" name="submitted" value="TRUE" /></form>
<br></br>
<br></br>

<?php
include ('includes/footer.html');
?>
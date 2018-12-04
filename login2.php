<?php
session_start();

$page_title = 'Login';
include ('loginfunctions.php');
require_once ('../connect3.php');

if (empty($_REQUEST['submitted']) || $_REQUEST['submitted']!=TRUE)
{
echo '
<h1>Login</h1>
<form action="login2.php" method="post">
<p align="center">ID:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="initials" size="18"  maxlength="20" /></p>
<p align="center">Password: <input type="password" name="pw" size="20" maxlength="25" /></p>
<br></br>
<p align="center"><input type="submit" name="submit" value="Login" /></p>
<input type="hidden" name="submitted" value="TRUE" /></form>
<br></br>
<br></br>';
}
else
{
   $id = $_REQUEST['initials'];
   //echo $id;
   $pw = $_REQUEST['pw'];
   //echo $pw;
   $sqlli = "Select pw from mds where initials='$id'";
   //echo $sqlli;
   $sqlq = mysql_query($sqlli);
   $sqlf = mysql_fetch_row($sqlq);
   if ($sqlf[0]==$pw)
   {
      $url = 'choose.php';
	  header("Location: $url");
	  exit();
   }
   else
      echo 'Try Again';
}
   

include ('includes/footer.html');
?>

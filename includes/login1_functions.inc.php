<?php # Script 11.2 - login_functions.inc.php

function absolute_url ($page = 'login1.php')
{
   $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
   $url = rtrim($url, '/\\');
   $url .='/'.$page;
   return $url;
}

function check_login($dbc, $initials='', $pass='')
{
   $errors = array();
   if (empty($initials))
   {
      $errors[] = 'You forgot to enter your ID.';
   }
   else
   {
      $e = mysqli_real_escape_string($dbc,trim($initials));
   }
   
   if (empty($pass))
   {
      $errors[] = 'You forgot to enter your Password.';
   }
   else
   {
      $p = mysqli_real_escape_string($dbc,trim($pass));
   }
   if (empty($errors))
   {
      $q = "SELECT initials, pass FROM mds WHERE initials='$e' AND pass=SHA1('$p')";
	  $r = @mysqli_query ($dbc, $q);
	  if (mysqli_num_rows($r) == 1)
	  {
	     $row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
		 return array(true, $row);
	  }
	  else
	  {
	     $errors[] = 'The ID and Password entered do not match those on file.';
	  }
   }
   return array(false, $errors);
}
?>
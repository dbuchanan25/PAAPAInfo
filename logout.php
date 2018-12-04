<?php #Script 11.11 - logout.php
session_start();

$_SESSION = array();
session_destroy();
setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0);


$page_title = 'Logged Out!';
include ('includes/header.php');
echo "<h1 align=center>Logged Out!</h1>
<p align=center> You are now logged out </p>
<br>
<br>
<br>";

include ('includes/footer.html');
?>

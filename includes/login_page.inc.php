<?php
if (!isset($_SESSION)) { session_start(); }

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


echo'
    <link rel="stylesheet" href="style.css" type="text/css">
    ';


echo'
<h1 class="h1log">Login</h1><br><br>


<form action="login.php" method="post">

<table width=100% align=center>
<tr>
    <td width=25%></td>
    <td width = 25% align=center height=50px style=background-color:#EEEEEE;>
        UserName: 
    </td>
    <td width = 25% align=center style=background-color:#EEEEEE;>
        <input type="text" name="initials" size="20"  maxlength="20" autofocus/>
    </td>
    <td width=25%></td>
</tr>
<tr>
    <td width=25%></td>
    <td width=25% align=center height=50px style=background-color:#EEEEEE;>
        Password: 
    </td>
    <td width=25% align=center style=background-color:#EEEEEE;>
        <input type="password" name="pass" size="20" maxlength="25" />
    </td>
    <td width=25%></td>
</tr>
</table>



<br>
<br>
<br>


<p class="p1">
<input type="submit" name="submit" value="Login" class="btn"/>
</p>
<input type="hidden" name="submitted" value="TRUE" />

</form>
<br>
<br>';

include ('includes/footer.html');
?>
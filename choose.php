<?php
if (!isset($_SESSION)) { session_start(); }

/*
 * Version 02_01
 */
/*
 * Last Revise 2011-06-12
 */
/*
 * LAST REVISED 2011-06-12 TO HELP INCORPORATE THE NEW PAY RULES AND FOR OVERALL UPDATING OF THE
 * PROGRAM.
 */

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
   echo '<title>'.$page_title.'</title>';

   require_once ($_SESSION['login2string']);

   include ('includes/header.php');
   $sqlpass = "SELECT number 
               FROM mds 
               WHERE initials='{$_SESSION['initials']}'";
   $sqlpassq = mysql_query($sqlpass);
   $sqlpassr = mysql_fetch_row($sqlpassq);

   $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'Sign On', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
   
   $sqlpass2 = "SELECT last 
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
		    echo '<form action="choose.php" method="post">';
		    $q = "UPDATE mds 
			  SET pass=SHA1('$np') 
                          WHERE initials='{$_SESSION['initials']}'";
			$r = mysql_query($q);
                    echo '<h2 align="center">You have successfully changed your password.
                          <br></h2>';
                    echo '<p align="center"><br>
                          <input type="submit" name="submit" value="Continue" />
                          </p></form>';
		 }
		 else
		 {
		    echo '<form action="choose.php" method="post">';
		    echo '<h1><center>Error!</h1>';
			echo '<p><center>The following errors occurred: <br />';
			   echo " - $errors[0]<br />\n";
			echo '</p><p>Please try again.</p><p><br /></p>';
			echo '<p align="center"><br>
                              <input type="submit" name="submit" value="Continue" />
			      </p></form>';
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
			  <p><center>
                          Confirm New Password: <input type="password" name="pass2" size="10"
			  maxlength="20" /> </p><br><br>
			  <p><center><input type="submit" name="submit" value="Change Password" />
                          </p>
			  <input type="hidden" name="submitted" value="TRUE" />
			  </form><p><br></p>';
      }
   }
   
   
   
   
   
   
   
   /////////////////////////////////////////////////////////////////////////////////////////
   //IF THE USER HAS AN APPROPRIATE PASSWORD AND HAS SIGNED ON CORRECTLY, THE PROGRAM     //
   //COMES HERE.                                                                          //
   /////////////////////////////////////////////////////////////////////////////////////////
   else
   {
   	   //$ini gets set to the $_SESSION['initials'] variable (the user who signed on)
   	   if (!isset($_SESSION['comeback']))
   	   {
   	   		$_SESSION['comeback']=$_SESSION['initials'];
   	   }
   	   else
   	   {
               if (isset($_SESSION['spmd'])) {
  			$_SESSION['comeback']=$_SESSION['spmd'];
               }
               else {
                    $_SESSION['comeback']=$_SESSION['initials'];
               }
   	   }
   	   $ini = $_SESSION['comeback'];
           
           include_once 'menuBar.php';
           menuBar(5663);
           echo '<br><br>';
    
    
	   echo'<form method="post" action="choose2.php">';
	   echo'
		<table align="center" class="content" border="0"
                    width="100%" bordercolor="#000000">
                    <tr>
			<td width="20%"></td>
			<td width="20%" align="center"> Choose Anesthesiologist: </td>
			<td width="20%"></td>
			<td width="20%" align="center">Choose Month: </td>
			<td width="20%"></td>
                    </tr>';
		 
		$result=mysql_query('SELECT first, last, initials 
		                     FROM mds 
                                     ORDER BY last');

		echo '<tr>
		      <td width="20%"></td>
		      <td width="20%" align="center"><select name="nameinitials">';
		while($row = mysql_fetch_row($result))
		{  
		  if ($row[2]==$ini)
		  {
		    echo "<option selected='selected' value=$row[2]>$row[0] $row[1]</option>\n";
		  }
		  else
		  {
		  	echo "<option value=$row[2]>$row[0] $row[1]</option>\n";
		  }  
		}
		  echo '</select></td><td width="20%"></td>';
		  
		
		$datetime = new DateTime('today');
		
		$_SESSION['dty']=$datetime->format('Y');
		
		$_SESSION['dtd']=$datetime->format('j');
		
		$dyear = $datetime->format('Y');
		$dy = $datetime->format('d');
		$dm = $datetime->format('n');

		if (!isset($_SESSION['dtm']))
		   $_SESSION['dtm']=$datetime->format('n');
		
		if ($_SESSION['initials']=='DB' || $_SESSION['initials']=='JB'
                        || $_SESSION['initials']=='PS')
		{
		   if ($dy<6)
		   {
		     $datetime->modify("-2 weeks");
                     $datetime->modify("-1 months");
		   }
		   else
		   {
		     $datetime->modify("-2 months");
		   }
		   
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		      echo '<td><center><select name="mn"><option selected="selected">'
		             .$datetime->format('F').'</option>\n';
		   }
		   else
		   {
		      echo '<td><center><select name="mn"><option>'
		             .$datetime->format('F').'</option>\n';
		   }
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   if ($dy<6)
		   {
		     $datetime->modify("-2 weeks");
		   }
		   else
		   {
		     $datetime->modify("-1 months");
		   }
		   
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		   	  echo '<option selected="selected">'.
                                $datetime->format('F').'</option>\n';
		   }
		   else
		   {
		      echo '<option>'.$datetime->format('F').'</option>\n';
		   }
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		      echo '<option selected="selected">'.$datetime->format('F').'</option>\n';
		   }
		   else
		   {
		   	  echo '<option>'.$datetime->format('F').'</option>\n';
		   }
		   
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		   	   echo '<option selected="selected">'.$datetime->format('F').'</option>';
		   }
		   else
		   {
		   	   echo '<option>'.$datetime->format('F').'</option>';
		   }
		}
		
		else if ($dy<4)
		{
		   $datetime->modify("-2 weeks");
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		      echo '<td width="20%"><center><select name="mn"><option selected="selected">'
		            .$datetime->format('F').'</option>\n';
		   }
		   else
		   {
		   	  echo '<td><center><select name="mn"><option>'
		   	        .$datetime->format('F').'</option>\n';
		   }
		   
		   $datetime->setDate($dyear,$dm,$dy);
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		      echo '<option selected="selected">'.$datetime->format('F').'</option>\n';
		   }
		   else
		   {
		   	echo '<option>'.$datetime->format('F').'</option>\n';
		   }
		   
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {   
		      echo '<option selected="selected">'.$datetime->format('F').'</option>';
		   }
		   else
		   {
		   	  echo '<option>'.$datetime->format('F').'</option>';
		   }
		}
		
		else
		{ 
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   { 
		      echo '<td><center><select name="mn"><option selected="selected">'
		            .$datetime->format('F').'</option>\n';
		   }
		   else
		   {
		   	  echo '<td><center><select name="mn"><option>'
		            .$datetime->format('F').'</option>\n';
		   }
		   if ($dy<25)
		      $datetime->modify("+1 months");
		   else
		      $datetime->modify("+2 weeks");
		   if ($_SESSION['dtm']==$datetime->format('n'))
		   {
		      echo '<option selected="selected">'.$datetime->format('F').'</option>';
		   }
		   else
		   {
		   	  echo '<option>'.$datetime->format('F').'</option>';
		   }
		}
		echo '</td><td width="20%"></td></tr>';
		echo '<tr>
		      </tr>
		      <tr>
			  <td>
			  </td>
			  <td>
			  </td>
			  <td align="center">
			  <input type="submit" name="submit" value="Submit" 
                                    class="btn">
			  </td>
			  </tr>';
		echo '</table></div></form><br><br>';
}

include ('includes/footer.html');
}
?>

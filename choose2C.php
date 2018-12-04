<?php
        if (!isset($_SESSION)) { session_start(); }
	require_once ('connect2.php');
	
	if ($_SESSION['superuser']!=1)
	{
		header( 'Location: http://localhost/Anesthesia_Pay/logout.php' ) ;	
	}
	
	else if ($_POST['ENTRY'])
	{
		$listofmdsq = "SELECT * FROM mds ORDER BY last";
		$listofmdsqu = mysql_query($listofmdsq);
		
		echo'
		    <body onLoad="document.name.mds.focus()">
			<form method="post" action="vac_page_1.php" name="name">';
				
		echo'
			<table align="center">
			<tr><th>CHOOSE THE PARTNER FOR WHO YOU WILL BE ENTERING VACATION CHOICES</th></tr></table>';
		echo'
			<br><br>
			<table align="center">
			<tr>
				<td align="center">Partners: </td></tr>
				<td id="mds" align="center">
				<select name="mds">';

		while ($md = mysql_fetch_row($listofmdsqu))
		{
			  	echo '<option value='.$md[6].'>'.$md[1].' '.$md[0].'</option>';
		}
		echo '<option value="BU"> Business Day </option>';


		echo'
			</select>
				</td>
			</tr>
			<tr>
				<td height=10px>
				</td>
				<td height=10px>
				</td>
			</tr>';

		echo '
           <tr style="height:50"><td height="25"></td></tr>
           <tr style="height:50">
		   <td align="center">
           <input type="submit" name="submit" value="Submit" style="width:200px; height:30px">
	       </td>
		   </table>
		   </form>';
	}
	
	else if ($_POST['QUICKENTRY'])
	{
		$_SESSION['firstpagequickentry']=1;
		include 'quickentry_trial.php';
	}
	
	else if ($_POST['DATABASE'])
	{
		   echo '<center><h2>Database Management</center></h2><br><br>
	             <body>';
		
		   echo '<table align="center" width="100%">
		         <form method="post" action="databasefunctions.php" class="input" name="name">
		         <tr>
		         <td align="center">
				 <input type="submit" name="VACFILE" value="Create and Download Vacation Text File" class="btn">
				 </td>
				 </tr>
				 <tr>
				 <td height="25"> 
				 </td>
				 </tr>
				 <tr>
		         <td align="center">
				 <input type="submit" name="RETURN" value="Return" class="btn">
				 </td>
				 </tr>
		         </table>		   
		         <br><br>';
	}
	
	else if ($_POST['LOGOUT'])
	{
		include 'logout.php';
	}
?>
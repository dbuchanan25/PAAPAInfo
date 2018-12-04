<?php
	session_start();
	require_once ('connect2.php');
	
	if ($_POST['VACFILE'])
	{
		$File = "VacationFile.txt";
		$Handle = fopen($File, 'w');
		
		$vacchosenquery = "SELECT anesthesiologistinitials, month, day, time 
		                   FROM vacation ORDER BY time";
		$vacchosenq = mysql_query($vacchosenquery);
		while ($vacchose = mysql_fetch_row($vacchosenq))
		{
			if ($vacchose[1]<10)
			{
			   $Data = 'Add: '.$vacchose[0].'  0'.$vacchose[1];	
			}
			else
			{
			   $Data = 'Add: '.$vacchose[0].'  '.$vacchose[1];	
			}
			if ($vacchose[2]<10)
			{
			   $Data = $Data.' 0'.$vacchose[2].'        entered at '.$vacchose[3]."\n";
			}
			else
		    {
			   $Data = $Data.' '.$vacchose[2].'        entered at '.$vacchose[3]."\n";
			}
			fwrite($Handle, $Data);
		}
	
		fclose($Handle);
		

		header('Content-disposition: attachment; filename=VacationFile.txt');
		header('Content-type: html/text');
		readfile('VacationFile.txt');
	}
	else if ($_POST['RETURN'])
	{
		include ('choose2B.php');
	}
	else if ($_POST['LOGOUT'])
	{
		include ('logout.php');
	}
?>
<?php 
		/*
		$confirmq = "SELECT anesthesiologistinitials, month, day, year, priority
		             FROM vacation
		             WHERE confirmation=0
		             ORDER BY year, month, day, priority";
		$confirmquery = mysql_query($confirmq);
		echo '<table align="center"><tr><th>NON-CONFIRMED VACATION DAYS</th></tr></table><br>
		      <table align="center" border=1><tr><th align="center">Initials</th>
		      									 <th align="center">Month</th>
		      									 <th align="center">Day</th>
		      									 <th align="center">Year</th>
		      									 <th align="center">Priority</th>
		      							     </tr>';
		if (mysql_num_rows($confirmquery)>0)
		{
			while($confirm = mysql_fetch_row($confirmquery))
			{
			echo'
					<tr>
						<td align="center">'.$confirm[0].'
						</td><td align="center">'.$confirm[1].
						'</td><td align="center">'.$confirm[2].
						'</td><td align="center">'.$confirm[3].
						'</td><td align="center">'.$confirm[4].
						'</td>
					</tr>';
			}
		}
		echo '</table>';
		*/
?>
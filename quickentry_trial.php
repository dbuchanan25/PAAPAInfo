<?php
session_start();
require_once ('connect2.php');

if ($_POST['Quit'])
{
	include ('choose2B.php');
	exit();
}
//////////////////////////////////////////////////////////////////////////////////
//IF AFTER THE DAYS ARE ENTERED IN THE QUICKENTRY PAGE, THE DAYS ARE NOT WANTED,//
//THEN THEY HAVE TO BE DELETED SINCE THEY WERE ADDED BEFORE VIEWING THE         //
//CALENDAR.                                                                     //
//////////////////////////////////////////////////////////////////////////////////
else if ($_POST['Cancel'])
{
	//////////////////////////////////////////////////////////////////////////////
	//$_SESSION['bdoy']
	//$_SESSION['edoy']
	//$_SESSION['initials']
	//////////////////////////////////////////////////////////////////////////////
	//PURPOSE:                                                                  //
	//Deletes the vacation days from the "vacation" table of the database.      //
	//////////////////////////////////////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////////////////////////////
	//Get the number of the anesthesiologist from initials                   //
	///////////////////////////////////////////////////////////////////////////
	if ($_SESSION['initials']!='BU')
	{
	  $r = "select number from mds where initials='{$_SESSION['initials']}'";
	  $r1 = mysql_query($r);
	  $schedmdnumber = mysql_fetch_row($r1);
	}
	else
	  $schedmdnumber[0] = 99; 
	
	///////////////////////////////////////////////////////////////////////////
	//Cycle through the days of vacation and delete them from the database.  //
	///////////////////////////////////////////////////////////////////////////
	$correctlydeleted = 1;
	$calreplaceresult1 = TRUE;
	$calreplaceresult2 = TRUE;
	for ($x=$_SESSION['bdoy']; $x<=$_SESSION['edoy']; $x++)
	{
		//////////////////////////////////////////////////////////////
		//Query the database to get the information for a particular//
		//day as identified by the numeric day of the year.         //
		//////////////////////////////////////////////////////////////
	    $dayquery = "SELECT * 
	                 FROM calendar2011 
	                 WHERE dayofyear = $x";
	    $dayq = mysql_query($dayquery);
	    $dayresult = mysql_fetch_row($dayq);
	    
		$vacinfoq = "SELECT * 
		             FROM calendar2011 
		             WHERE month=$dayresult[1] 
					 AND	day=$dayresult[2]";
	
		$vacinfoquery = mysql_query($vacinfoq);
		$vacinforesult = mysql_fetch_row($vacinfoquery);
		
		$vacinfowaitlist = $vacinforesult[7];
		$vacinfouncredited = $vacinforesult[6];
		$vacinfocredited = $vacinforesult[4];
		

		
		if ($vacinfowaitlist > 0)
		{
			$calreplaceq = "UPDATE calendar2011 
			                SET waitlist = $dayresult[7]-1 
			                WHERE dayofyear = $x";
			$calreplaceresult2 = mysql_query($calreplaceq);
		}
		else if ($vacinfouncredited > 0)
		{
			$calreplaceq1 = "UPDATE calendar2011 
			                 SET uncredited = $dayresult[5]+1
							 WHERE dayofyear = $x";
			$calreplaceresult1 = mysql_query($calreplaceq1);
			
			$calreplaceq2 = "UPDATE calendar2011 
			                 SET uncreditedtaken = $dayresult[6]-1
			                 WHERE dayofyear = $x";
			$calreplaceresult2 = mysql_query($calreplaceq2);
		}
		else if ($vacinfocredited > 0)
		{
			$calreplaceq1 = "UPDATE calendar2011 
			                 SET credited = $dayresult[3]+1
			                 WHERE dayofyear = $x";
			$calreplaceresult1 = mysql_query($calreplaceq1);
			
			$calreplaceq2 = "UPDATE calendar2011 
			                 SET creditedtaken = $dayresult[4]-1
			                 WHERE dayofyear = $x";
			$calreplaceresult2 = mysql_query($calreplaceq2);
		}
		else
		{
			$correctlydeleted = 3;
		}
		
		//////////////////////////////////////////////////////////////
	    //Decrease the priorities of all vacationers above the      //
	    //current one.                                              //
	    //////////////////////////////////////////////////////////////
	    
		$priorityresult = 1;
		
	    //////////////////////////////////////////////////////////////
	    //Delete the vacation day from                              //
	    //"vacation" table.                                         //
	    //////////////////////////////////////////////////////////////
		$okdeleted = mysql_query ("DELETE 
		                           FROM vacation 
		                           WHERE anesthesiologistinitials='{$_SESSION['draftinitials']}'
								   AND month=$dayresult[1] 
								   AND day=$dayresult[2]"
								 );
								 
		if (!$okdeleted || !$calreplaceresult1 || !$calreplaceresult2)
		{
			$correctlydeleted = 0;
		}
		if ($priorityresult==0)
		   	$correctlydeleted = 2;
	}

	if ($correctlydeleted==1)
	{
		$_SESSION['firstpagequickentry']=1;
	}
	else if ($correctlydeleted!=1)
	{
		echo'
			<table align="center">
				<tr>
					<td align="center">There was an error deleting vacation from the database.</td>
					<td align="center">PLEASE CONTACT DALE BUCHANAN.</td>
				</tr>
				<tr>
					<td align="center">Error Code: '.$correctlydeleted.'</td>
				</tr>
			</table>
			';
		
		echo'
		<br><br>
		<table align="center">		
			<form method="post" action="vac_page_2.php" class="input">
			    <tr>
	      			<td align="center">
	      				<input type="submit" name="View" value="RETURN" class="btn">
	          		</td>
	          	</tr>
	        </form>
	    </table>
	    ';
	}
}
//////////////////////////////////////////////////////////////////////////////////
//END OF THE DELETE CODE.                                                       //
//////////////////////////////////////////////////////////////////////////////////











//////////////////////////////////////////////////////////////////////////////////
//THIS PART OF THE CODE PRESENTS THE QUICK ENTRY PART OF THE PAGE               //
//////////////////////////////////////////////////////////////////////////////////
if ($_SESSION['firstpagequickentry']==1)
{
	$_SESSION['firstpagequickentry']=0; 
?>

      
	<body onLoad="document.f1.dates.focus()">
	<form method=post action="quickentry_trial.php" name=f1>
	<table align="center">
	   <tr>
	      <td align="center">
	         <font size=6>
	         <b>
	         Enter Vacation
	         </b>
	         </font>
	      </td>
	   </tr>
	   <tr>
	      <td align="center">
	         <b>Format is: </b>Partner Initials (space) Begin Date - Month and Day (space) End Date (optional, if same as Begin Date) - Month (optional, if same as Begin Month) and Day
	      </td>
	   </tr>
	   <tr>
	      <td align="center">
	         <b>
	         Examples:
	         </b>
	      </td>
	   </tr>
	   <tr>
	      <td align="center">
	         CY 0101 0101
	      </td>
	   </tr>
	   <tr>
	      <td align="center">
	         CY 0101 01
	      </td>
	   </tr>
	   <tr>
	      <td align="center">
	         CY 0101
	      </td>
	   </tr>
	   <tr height=25px>
	   </tr>
	   <tr>
	      <td align="center">
	<input type=text name='dates'>
		  </td>
	   </tr>
	   <tr>
	      <td align="center">
	<input type=submit value=Submit>
	      </td>
	   </tr>
	   <tr height=200px>
	   </tr>
	   </table>
	</form>
	</body>
	
	<form method="post" action="quickentry_trial.php" class="input">
	<table align="center">
	   <tr>
          <td align="center">
		  <input type="submit" name="Quit" value="Quit" class="btn">
		  </td>
       </tr>
     </table>
     </form>     

	
	
	<?php 
}

//////////////////////////////////////////////////////////////////////////////////
//END OF THE FIRST PART OF THE PAGE CODE                                        //
//////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////
//WHEN THE USER SUBMITS THE DATE, THE SESSION VARIABLE firstpagequickentry IS   //
//EQUAL TO 0 AND COMES HERE.                                                    //
//THIS PART OF THE CODE CHECKS FOR PROPER FORMATTING OF THE ENTRY STRING AND    //
//THEN ENTERS THE DATES, IF CORRECT.                                            //
//////////////////////////////////////////////////////////////////////////////////
else
{
	$_SESSION['firstpagequickentry']=1;
	$strmdsded = $_POST['dates'];
	
	if (strlen($strmdsded)==12)
	{
		$initials = $strmdsded[0].$strmdsded[1];
		$beginmonth = $strmdsded[3].$strmdsded[4];
		$beginday = $strmdsded[5].$strmdsded[6];
		$endmonth = $strmdsded[8].$strmdsded[9];
		$endday = $strmdsded[10].$strmdsded[11];
	}
	else if (strlen($strmdsded)==10)
	{
		$initials = $strmdsded[0].$strmdsded[1];
		$beginmonth = $strmdsded[3].$strmdsded[4];
		$beginday = $strmdsded[5].$strmdsded[6];
		$endmonth = $strmdsded[3].$strmdsded[4];
		$endday = $strmdsded[8].$strmdsded[9];
	}
	else if (strlen($strmdsded)==7)
	{
		$initials = $strmdsded[0].$strmdsded[1];
		$beginmonth = $strmdsded[3].$strmdsded[4];
		$beginday = $strmdsded[5].$strmdsded[6];
		$endmonth = $strmdsded[3].$strmdsded[4];
		$endday = $strmdsded[5].$strmdsded[6];
	}
	else
	{
		echo 
		'
		<table align=center>
		   <tr>
		      <td align=center>
		         <b>You entered the information in an incorrect format!</b>
		      </td>
		   </tr>
		   <tr>
		      <td align=center>
		         Please try again.
		      </td>
		   </tr>
		   <tr>
		      <td align=center>
		<a href="quickentry_trial.php">Try Again</a>
		      </td>
		   </tr>
		</table>';
		exit;	
	}
	$initials = strtoupper($initials);
	
	$checkinitialsq = "SELECT * 
	                   FROM mds
	                   WHERE initials='$initials'";
	$checkinitialsquery = mysql_query($checkinitialsq);
	$doesExist = (bool) @($res = mysql_fetch_array($checkinitialsquery));
	
	if (!$doesExist)
	{
		echo 
		'
		<table align=center>
		   <tr>
		      <td align=center>
		         <b>You entered unknown initials!</b>
		      </td>
		   </tr>
		   <tr>
		      <td align=center>
		         Please try again.
		      </td>
		   </tr>
		   <tr>
		      <td align=center>
		<a href="quickentry_trial.php">Try Again</a>
		      </td>
		   </tr>
		</table>';
		exit;	
	}
	
	$formd = $initials;
	$_SESSION['initials']=$initials;
	$_SESSION['beginmonth']=$beginmonth;
	$_SESSION['endmonth']=$endmonth;
	$_SESSION['beginday']=$beginday;
	$_SESSION['endday']=$endday;
	
	
	
	//////////////////////////////////////////////////////////////////////////
	//THIS IS THE VAC_ADD_CHECK CODE                                        //
	//////////////////////////////////////////////////////////////////////////
	$_SESSION['draftinitials']=$initials;
		
	//////////////////////////////////////////////////////////////////////////////	
	//Get the numerical day of the year from the database table "calendar2011"  //
	//if it exists (assuming the user hasn't made a mistake in choosing the     //
	//particular days).  Both the beginning and the end vacation days are       //
	//obtained.                                                                 //
	//////////////////////////////////////////////////////////////////////////////	
		$begindayofyearquery = "SELECT dayofyear 
		                        FROM calendar2011 
		                        WHERE month='$beginmonth' 
		                        AND day='$beginday'";
		$enddayofyearquery = "SELECT dayofyear 
		                      FROM calendar2011 
		                      WHERE month='$endmonth' 
		                      AND day='$endday'";
		
		$bdoyq = mysql_query($begindayofyearquery);
		$edoyq = mysql_query($enddayofyearquery);
		
	///////////////////////////////////////////////////////////////////////////////
	//The "if" statement checks to see if the days exist.                        //
	///////////////////////////////////////////////////////////////////////////////	
		if (mysql_num_rows($bdoyq)!=0 && mysql_num_rows($edoyq)!=0)
		{
			$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
			
			$bdoyqresult = mysql_fetch_row($bdoyq);	
			$edoyqresult = mysql_fetch_row($edoyq);
			
	///////////////////////////////////////////////////////////////////////////////
	//This "if" statement makes sure the end date is not before the beginning    //
	//date and that the user is not trying to pick a date before today's allowed //
	//future date for vacation choice.                                           //
	///////////////////////////////////////////////////////////////////////////////		
			if (($edoyqresult[0]-$bdoyqresult[0])<0
			 || (($datetime->format('m') >= $monthbegin) && ($datetime->format('Y') == 2011)))
			{
				echo '<table align="center">
				<tr>
					<th align="center">		
					Either the month is not available or the end day you picked is before 
					the beginning day.
					</th>
				</tr>
				<tr>
					<td align="center">
					Please check and make sure the days you are choosing are correct.
					</td>
				</tr>';
	
				/////////////////////////////////////////////////////////////////////////////
				//If either of these conditions exist, the user is sent back to the        //
				//beginning.                                                               //
				/////////////////////////////////////////////////////////////////////////////
				$_SESSION['firstpagequickentry']=1;
				echo'
				<form method="post" action="quickentry_trial.php" class="input">
				    <tr>
		      			<td align="center">
		      				<input type="submit" name="over" value="Start Over" class="btn">
		          		</td>
		          	</tr>
		          </form>
				</table>';
			}
			else
			{
	/////////////////////////////////////////////////////////////////////////////////////////////
	//If the days have been entered correctly, then a check is made to make sure they are not  //
	//full.                                                                                    //
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	//"$available" indicates whether the days are available and is initially set to 1 (TRUE)   //
	//With checking, if it is found a day is not available then "$available to is set to       //
	//0 (FALSE).                                                                               //
	/////////////////////////////////////////////////////////////////////////////////////////////
				$available = 1;
				$repeatvacday = 0;
	
				for ($x=$bdoyqresult[0]; $x<=$edoyqresult[0]; $x++)
				{
					$checkvacdayavailabilityquery = "SELECT credited, creditedtaken, 
					           uncredited, uncreditedtaken FROM calendar2011 WHERE dayofyear=$x";
					$cvdaq = mysql_query($checkvacdayavailabilityquery);
					$cvdaqresult = mysql_fetch_row($cvdaq);
					
					if (($cvdaqresult[0] + $cvdaqresult[2])==0)
						$available=0;
				}
				
				for ($x=$bdoyqresult[0]; $x<=$edoyqresult[0]; $x++)
				{
					////////////////////////////////
					//First, get the month and day//
					////////////////////////////////
					$getdayquery = "SELECT month, day FROM calendar2011 WHERE dayofyear=$x";
					$getdayq = mysql_query($getdayquery);
					$getdayresult = mysql_fetch_row($getdayq);
					$monthsvd = $getdayresult[0];
					$daysvd = $getdayresult[1];
		
					//////////////////////////////////////////////////////
					//Then, see if the user has already chosen this day.//
					//////////////////////////////////////////////////////
					$samevacdayquery = "SELECT * FROM vacation WHERE 
								anesthesiologistinitials='$initials' AND
								month='$monthsvd' AND day='$daysvd'";
					$samevacdayq = mysql_query($samevacdayquery);
					if (mysql_num_rows($samevacdayq)!=0)
						$repeatvacday=1;		
				}
				
				
	////////////////////////////////////////////////////////////////////////////////////
	//HERE, THE CODE MAKES AVAILABLE THE WAIT LIST OPTION SINCE THERE ARE NO AVAILABLE//
	//DAYS.                                                                           //
	//IN THIS SITUATION IT WOULD BE BEST TO THROW UP AN ALERT AND THEN ALLOW THE      //
	//ENTRY.                                                                          //
	////////////////////////////////////////////////////////////////////////////////////			
				if ($available==0 && $repeatvacday==0)
				{
					$_SESSION['bdoy']=$bdoyqresult[0];
					$_SESSION['edoy']=$edoyqresult[0];
					$waitlist=1;
				}
	
				else if ($repeatvacday==1)
				{
						echo '<table align="center">
					<tr>
						<th>		
						You have already chosen at least one of these days for vacation.
						</th>
					</tr>
					<tr>
						<td>
						Please check and make sure the days you are choosing are correct.
						</td>
					</tr>';
						
					/////////////////////////////////////////////////////////////////////////////
					//If either of these conditions exist, the user is sent back to the        //
					//beginning.                                                               //
					/////////////////////////////////////////////////////////////////////////////
					$_SESSION['firstpagequickentry']=1;
					echo'
					<form method="post" action="quickentry_trial.php" class="input">
					    <tr>
			      			<td align="center">
			      				<input type="submit" name="Over" value="Start Over" class="btn">
			          		</td>
			          	</tr>
			          </form>
					</table>';
					exit;
				}
				
	/////////////////////////////////////////////////////////////////////////////
	//If everything looks OK, the beginning and ending days are given          //
	//"$_SESSION" variable status and control is transferred to                //
	//"vac_add_confirm.php" where the user must confirm these are the days     //
	//he wants for vacation.                                                   //
	/////////////////////////////////////////////////////////////////////////////
				else
				{
					$_SESSION['bdoy']=$bdoyqresult[0];
					$_SESSION['edoy']=$edoyqresult[0];
				}
			}
		}
		
	/////////////////////////////////////////////////////////////////////////////
	//If the days do not exist (ie picking February 31), the user is sent back //
	//to the beginning.                                                        //
	/////////////////////////////////////////////////////////////////////////////
		else
		{
			$_SESSION['firstpagequickentry']=1;
			echo '<table align="center">
			<tr>
				<th>		
				One or both of the days you have picked do not exist.
				</th>
			</tr>
			<tr>
				<td>
				Please check and make sure the days you are choosing are correct.
				</td>
			</tr>
			
			<form method="post" action="quickentry_trial.php" class="input">
			    <tr>
	      			<td align="center">
	      				<input type="submit" name="Over" value="Start Over" class="btn">
	          		</td>
	          	</tr>
	          </form>
			</table>';
			exit;
		}	
	//END OF VAC_ADD_CHECK CODE
		
	//VAC_ADD_WRITE CODE
	///////////////////////////////////////////////////////////////////////////
	//Get the number of the anesthesiologist from initials                   //
	///////////////////////////////////////////////////////////////////////////
	if ($initials!='BU')
	{
	  $r = "select number from mds where initials='$initials'";
	  $r1 = mysql_query($r);
	  $schedmdnumber = mysql_fetch_row($r1);
	}
	else $schedmdnumber[0]=99; 
	
	///////////////////////////////////////////////////////////////////////////
	//Cycle through the days of vacation and enter them into the database.   //
	///////////////////////////////////////////////////////////////////////////
	$correctlyinserted = 1;
	for ($x=$_SESSION['bdoy']; $x<=$_SESSION['edoy']; $x++)
	{
		//////////////////////////////////////////////////////////////
		//Query the database to get the information for a particular//
		//day as identified by the numeric day of the year.         //
		//////////////////////////////////////////////////////////////
	    $dayquery = "SELECT * FROM calendar2011 WHERE dayofyear = $x";
	    $dayq = mysql_query($dayquery);
	    $dayresult = mysql_fetch_row($dayq);
	    
	    //////////////////////////////////////////////////////////////
	    //Determine the priority (where on the list) for the        // 
	    //vacation day.                                             //
	    //////////////////////////////////////////////////////////////
	    $priority = $dayresult[3]-($dayresult[3]-$dayresult[4]) + 
	                $dayresult[5] - ($dayresult[5]-$dayresult[6])+
	                $dayresult[7] + 1;
	    
	    //////////////////////////////////////////////////////////////
	    //Determine whether it is a credited vacation day.          //
	    //////////////////////////////////////////////////////////////
	    if ($dayresult[3]>0)
	    	$credited = 1;
	    else if ($dayresult[5]>0)
	    	$credited = 0;
	    else
	    	$credited = -1;
	
	    //////////////////////////////////////////////////////////////
	    //Insert the values for the particular fields into the      //
	    //"vacation" table.                                         //
	    //////////////////////////////////////////////////////////////
		$okinserted=mysql_query ("INSERT INTO vacation VALUES
		         
														 ('0', 
													      '$schedmdnumber[0]',
													      '{$_SESSION['initials']}',
												          '$dayresult[1]',
												          '$dayresult[2]',
												          '2011',
												          '$priority',
												          '$dayresult[8]',
												          '$credited',
												          now(),
												          'NULL'
												          )"
								);
		if (!$okinserted)
			$correctlyinserted = 0;	
	
		if ($credited==1)
		{	
			$updatecreditednoncredited = mysql_query ("UPDATE calendar2011 SET 
														credited=credited-1,
														creditedtaken=creditedtaken+1
														WHERE dayofyear='$x'
													 ");
			if (!updatecreditednoncredited)
			{
				echo 'Error in updating credited vacation.  PLEASE CONTACT DALE BUCHANAN.';
			}
		}
		else if ($credited==0)
		{
			$updatecreditednoncredited = mysql_query ("UPDATE calendar2011 SET 
														uncredited=uncredited-1,
														uncreditedtaken=uncreditedtaken+1
														WHERE dayofyear='$x'
													 ");
			if (!updatecreditednoncredited)
			{
				echo 'Error in updating uncredited vacation.  PLEASE CONTACT DALE BUCHANAN.';
			}
		}
		else if ($credited==(-1))
		{
			$updatecreditednoncredited = mysql_query ("UPDATE calendar2011 SET 
														waitlist = waitlist + 1
														WHERE dayofyear='$x'
													 ");
			if (!updatecreditednoncredited)
			{
				echo 'Error in updating uncredited vacation.  PLEASE CONTACT DALE BUCHANAN.';
			}
		}
}
	
	
	















/////////////////////////////////////////////////////////////////////////////////////////
//If all the values have been entered correctly, then "$correclyinserted" will remain 1//
//and the user will be so notified.  Then the user will be redirected to the "view"    //
//page.                                                                                //
///////////////////////////////////////////////////////////////////////////////////////// 
if ($correctlyinserted==1)
{



	include ('arch.php');
	echo'
		<LINK REL=StyleSheet HREF="style1.css" TYPE="text/css">
		<html>
		<title>Vacation Choosing Page</title>';
		
		echo'
		<head> 
		</head>
		<body onLoad="document.f1.Accept.focus()">
		<div id="leftcol">';
		
		
		
	
	/*
	  $dimo                = days in month
	  $dty                 = current year
	  $mno                 = month (numerical) ie January=1
	  $mn                  = month (alphabetical)
	  $frow                = user's name (first & last)
	  $firstdayofweek      = Monday=1...Sunday=7 for the selected month
	  $formd               = initials for the schedule for physician
	  
	  $_SESSION['schedmd'] = initials for the "schedule for" physician
	  $_SESSION['schedmdnum'] = md number for the "schedule for" physician
	  $_SESSION['dty']     = year
	*/
	
	
	  $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//..$_SESSION['yr'] WOULD BE SET ON THE PREVIOUS PAGE WHEN THE USER PICKED THE YEAR THEY WANTED...........................//
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $_SESSION['yr'] = 2011;
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	//THIS IS TEMPORARY.......................................................................................................//
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	  
	  $dty = $_SESSION['yr'];
	  $mno = $beginmonth;
	 
	 ////////////////////////////////////////////////////////////
	 /*This section prints the days of the week across the page*/
	 ////////////////////////////////////////////////////////////
	 
	 /*!!!!This should be done as a percentage of the width of the page!!!*/
	 
	 while ($mno<$beginmonth+2)
	 {
	 $datetime->setDate($dty,$mno,1);
	 $monthname = $datetime->format('F Y'); 
	 
	 echo'
	    
	    <table border="0" cellspacing="0" width="100%">  
	     <tr>
	     <td bgcolor="#ffffff" width="100%" height="30px" align="center" style="font-size:large">'.$monthname.'</td>
	     </tr>
	    </table>
	    
	    
	    <table border="0" cellspacing="0"> 
	    <tr>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Sun
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Mon
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Tue
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Wed
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Thu
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Fri
			   </td>
	          </tr>
			</table>
		 </td>
		 <td  width="14%" height="20" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="center" style="font-size:small">
			   Sat
			   </td>
	          </tr>
			</table>
		 </td>
		 </tr>';
		 
	
	 
	  $numericaldayofmonth = 0; 
	  $datetime->setDate($dty,$mno,1);
	  $dimo = cal_days_in_month(CAL_GREGORIAN, $mno, $dty) ;
	  $firstdayofweek = $datetime->format('N');
	   switch ($firstdayofweek)
	   {
	     case '1':
	     echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>';
		
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
		 
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   echo'
		</tr>
		<tr>'; 
	    break;
		
		
	    case '2':
		 echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;   
	   
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '3':
		 echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
	
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '4':
		 echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   echo' 
	    </tr> 
		<tr>';
		break;  
		
		case '5':
		 echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
		  
	   
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   $vacinfo=NULL;  
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   $vacinfo=NULL;
	   echo'
	    </tr>
		<tr>';
		break;
		
		case '6':
		 echo'
		 <tr>  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
		 
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		  
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		
	     <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 
	      
		 <td bgcolor="#ffffff" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="#ffffff" height="20" width="150" align="left" style="font-size:small">
			   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="#ffffff" height="65" width="150">
			   
			   </td>
	          </tr>
			</table>
		 </td>';
		 
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   echo'
		</tr>
		<tr>';
		break;
		
		case '7':
	     echo'
		 <tr>'; 
		 
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	   
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++; 
		 
		 echo'
		 </tr>
		 <tr>'; 
	     break;
		 };
		 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//From here is the middle of the calendar after the first day is situated.                                 //
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 echo'  
		</tr><tr>';
		
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		
	    echo'
		</tr><tr>';	
		
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		 
		 arch($numericaldayofmonth, $mno);
		 $numericaldayofmonth++;
		
	     echo'
		 </tr>';
		
	if ($numericaldayofmonth<$dimo)
	{
	   echo '
	   <tr>';
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	if ($numericaldayofmonth<$dimo)
	{
	if ($numericaldayofmonth<$dimo)
	{
	   echo '</tr><tr>';
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	
	else
	{
	    echo'
		<tr>
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 ';
	}
	
	if ($numericaldayofmonth<$dimo)
	{
	   arch($numericaldayofmonth, $mno);
	   $numericaldayofmonth++;
	}
	else
	{
	    echo'
	    <td bgcolor="black" width="150" height="85" align="center">
	        <table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			   <td bgcolor="black" height="20" width="150" align="left" style="font-size:small">		   
			   </td>
	          </tr>
	          <tr>
			   <td bgcolor="black" height="65" width="150">	   
			   </td>
	          </tr>
			</table>
		 </td>
		 </tr>
		 ';
	}
	}
		
	echo'
	    </table>
	 ';
	$mno++;
	}
	
	echo'
	</div>';
	
	if ($_SESSION['beginday']>22)
	{
	?>
	
	
	<script type="text/javascript">
	var div = document.getElementById("leftcol");   
	div.scrollTop = 400;
	</script> 
	
	
	
	<?php 
	}
    else if ($_SESSION['beginday']>15)
	{
	?>
	
	
	<script type="text/javascript">
	var div = document.getElementById("leftcol");   
	div.scrollTop = 200;
	</script> 
	
	
	
	<?php 
	}
	
	
$mdnameq = "SELECT first, last
            FROM mds
            WHERE initials='$initials'";
$mdnamequery = mysql_query($mdnameq);
$mdnamea = mysql_fetch_row($mdnamequery);
$mdname = $mdnamea[0].' '.$mdnamea[1];

$begindowq = "SELECT dayofweekname
              FROM calendar2011
              WHERE month=$beginmonth
              AND day=$beginday";
$begindowquery = mysql_query($begindowq);
$begindowa = mysql_fetch_row($begindowquery);
$begindow = $begindowa[0];

$enddowq = "SELECT dayofweekname
            FROM calendar2011
            WHERE month=$endmonth
            AND day=$endday";
$enddowquery = mysql_query($enddowq);
$enddowa = mysql_fetch_row($enddowquery);
$enddow = $enddowa[0];








echo'
<form method=post action="quickentry_trial.php" name=f1> 
<table align=center>
   <tr>
      <td align=center>
         <font size=5>
         <b>
         Vacation Entered:
         </b>
         </font>
      </td>
   </tr>
</table>
<table align=center>
   <tr>
      <td align=center>
         <b>Name:</b>
      </td>
      <td align=center>'.
      $mdname.'
      </td>
   </tr>
   <tr>
      <td align=center>
         <b>From:</b>
      </td>
      <td align=center>'.
      $begindow.'., '.$beginmonth.'/'.$beginday.'
      </td>
   </tr>
   <tr>
      <td align=center>
         <b>To:</b>
      </td>
      <td align=center>'.
      $enddow.'., '.$endmonth.'/'.$endday.'
      </td>
   </tr>';
	if ($waitlist==1)
	{
		echo'
		</table>
		<table align=center>
			<tr>
			   <td align=center bgcolor=red>
			      At least one of the days is a WAITLIST DAY!
			   </td>
			</tr>';
		$waitlist=0;
	}
	echo'
	</table>
	<br>
	<table align=center> 
	   <tr>
	      <td align=center>
	<input type=submit value=Accept name=Accept>
	      </td>
	   </tr>
	   <tr>
	      <td align=center>
	<input type=submit value=Cancel name=Cancel>
	      </td>
	   </tr>
	</table>
	</form>';
	}


	else if ($correctlyinserted!=1)
	{
		echo'
			<table align="center">
				<tr>
					<td align="center">There was an error entering values into the database.</td>
					<td align="center">PLEASE CONTACT DALE BUCHANAN.</td>
				</tr>
			</table>
			';
		$_SESSION['firstpagequickentry']=1;
		echo'
		<br><br>
		<table align="center">		
			<form method="post" action="quickentry_trial.php" class="input">
			    <tr>
	      			<td align="center">
	      				<input type="submit" name="View" value="RETURN" class="btn">
	          		</td>
	          	</tr>
	        </form>
	    </table>
	    ';
	}
}
?>



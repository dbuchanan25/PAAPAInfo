<?php
require_once ('../connect2.php');

   $pedanesthesiologist = array(14, 25, 35, 40, 41, 43, 47, 53, 56, 58);
   $nondayassignments = array('C Mat', 'C Hnt', 'H 1  ', 'Vac  ', 'C OH ', 'C OB ', 'OrOff', 'ObOff', 'H Off', 'M Off', 'ObOff');
   $nonnextdayassignments = array('C Mat', 'C Hnt', 'Vac  ', 'ObOff', 'OrOff');
   
   
   $s = "SELECT monthnumber, daynumber, mdnumber 
         FROM monthassignment 
         WHERE yearnumber='2010'AND assignment='Peds Call'
         ORDER BY monthnumber, daynumber";
   $sq = mysql_query($s);
   
   echo '<table align="left" border="1">
   
       <h1 align="center">
	   Peds Call Report
	   </h1> <tr>';
   
   while ($sr = mysql_fetch_row($sq))
   {
	   if ($sr[1]==1)
	   {
	   	   $mon=$sr[0]-1;
	   	   if ($mon==1 OR $mon==3 OR $mon==5 OR $mon==7 OR $mon==8 OR $mon==10)
	   	      $d = 31;
	   	   else if ($mon==2)
	   	      $d = 28;
	   	   else
	   	      $d = 30;
	   }
	   else
	   {
	       $mon=$sr[0];
	       $d=$sr[1]-1;
	   }
	   
	   if (($sr[0]==1 OR $sr[0]==3 OR $sr[0]==5 OR $sr[0]==7 OR $sr[0]==8 OR $sr[0]==10)
	        AND $sr[1]==31)
		   {
		   	   $mon2 = $sr[0]+1;
		   	   $d2 = 1;
		   }
	   else if ($sr[0]==2 AND $sr[1]==28)
		   {
		       $mon2 = $sr[0]+1;
		       $d2 = 1;
		   }
	   else if (($sr[0]==4 OR $sr[0]==6 OR $sr[0]==9 OR $sr[0]==11) AND $sr[1]==30)
		   {
		       $mon2 = $sr[0]+1;
		       $d2 = 1;
		   }
	   else
	   {
	       $mon2 = $sr[0];
	       $d2 = $sr[1]+1;
	   }
   
	   for ($x=0; $x<10; $x++)
	   {
	       $eligible = 1;
	       $done = 0;
		   $hs = "SELECT mdnumber, assignment, monthnumber, daynumber FROM monthassignment 
		   WHERE mdnumber=$pedanesthesiologist[$x] 
		   AND monthnumber=$mon AND daynumber=$d AND yearnumber='2010' AND assigntype=1";
		   $hsq = mysql_query($hs);
	   
		   while ($hsqr = mysql_fetch_row($hsq))
		   {
		      if ($hsqr[0]=='Vac  ')
		         $eligible=0;
		      if ($sr[2]==$pedanesthesiologist[$x])
		      {
		         $done=1;
		         echo '<tr bgcolor=#77ffff><td>'.$hsqr[0].'</td><td>'.$hsqr[1].'</td><td>'.$hsqr[2].'</td><td>'.$hsqr[3].'</td>';
		      }
		      else
		         echo '<tr bgcolor=#ffffff><td>'.$hsqr[0].'</td><td>'.$hsqr[1].'</td><td>'.$hsqr[2].'</td><td>'.$hsqr[3].'</td>';
		   }
		   
	       $hs1 = "SELECT assignment, monthnumber, daynumber FROM monthassignment 
		   WHERE mdnumber=$pedanesthesiologist[$x] 
		   AND monthnumber=$sr[0] AND daynumber=$sr[1] AND yearnumber='2010' AND assigntype=1";
		   $hsq1 = mysql_query($hs1);
	   
		   while ($hsqr1 = mysql_fetch_row($hsq1))
		   {
		       for ($xx=0; $xx<12; $xx++)
			   {

			   		if ($hsqr1[0]==$nondayassignments[$xx])
			   		    $eligible=0;
			   }
		        echo '<td>'.$hsqr1[0].'</td><td>'.$hsqr1[1].'</td><td>'.$hsqr1[2].'</td>';
		   }
		   
	       $hs2 = "SELECT assignment, monthnumber, daynumber FROM monthassignment 
		   WHERE mdnumber=$pedanesthesiologist[$x] 
		   AND monthnumber=$mon2 AND daynumber=$d2 AND yearnumber='2010' AND assigntype=1";
		   $hsq2 = mysql_query($hs2);
	   
		   while ($hsqr2 = mysql_fetch_row($hsq2))
		   {
			   for ($xxx=0; $xxx<5; $xxx++)
		   	   {
			   		if ($hsqr2[0]==$nonnextdayassignments[$xxx])
			   		    $eligible=0;
			   }
		      echo '<td>'.$hsqr2[0].'</td><td>'.$hsqr2[1].'</td><td>'.$hsqr2[2].'</td>';
		   }
		      
		   echo '<td>'.$eligible.'</td><td>'.$done.'</td></tr>';
	   }	  
		   echo '<tr><td height=20px></td></tr>';
   }
   
   echo '</table>';
?>


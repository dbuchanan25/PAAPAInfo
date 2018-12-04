<?php
if (!isset($_SESSION)) { session_start(); }
require_once ('connect2.php');
include ('calldisplay.php');

if ($_POST['PAMS'])
   include "choose.php";
   
else if ($_POST['PS'])
{
	 echo '<center><h2>Patient Satisfaction Survey Data</center></h2>
     <center><h2>For: '.$_SESSION['initials'].'</center></h2><br>';
	 
	 $n = array();
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q1>0 AND q1<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q2>0 AND q2<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q3>0 AND q3<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q4>0 AND q4<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q5>0 AND q5<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q6>0 AND q6<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q7>0 AND q7<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q8>0 AND q8<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q9>0 AND q9<3 AND questionset=1";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 
	 
	 $sum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers
				WHERE q1>0 AND q1<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum1 += $groupa[0];
	 
	 $sum2 = 0;
	 $groupq = "SELECT q2 
	            FROM answers
				WHERE q2>0 AND q2<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum2 += $groupa[0];	
	 
	 $sum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers
				WHERE q3>0 AND q3<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum3 += $groupa[0];	
	 
	 $sum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers
				WHERE q4>0 AND q4<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum4 += $groupa[0];	  
	 
	 $groupq = "SELECT q5 
	            FROM answers
				WHERE q5>0 AND q5<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum5 += $groupa[0];	
	 
	 $groupq = "SELECT q6 
	            FROM answers
				WHERE q6>0 AND q6<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum6 += $groupa[0];		
	 
	 $groupq = "SELECT q7 
	            FROM answers
				WHERE q7>0 AND q7<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum7 += $groupa[0];	 
	 
	 $groupq = "SELECT q8 
	            FROM answers
				WHERE q8>0 AND q8<6 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum8 += $groupa[0];	 
	 
	 $groupq = "SELECT q9 
	            FROM answers
				WHERE q9>0 AND q9<3 AND questionset=1";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum9 += $groupa[0];	
		   
	 $meangroup = array();
	 $x = 0;
	 $meangroup[] = $sum1/$n[$x];
	 $x++;
	 $meangroup[] = $sum2/$n[$x];
	 $x++;
	 $meangroup[] = $sum3/$n[$x];
	 $x++;
	 $meangroup[] = $sum4/$n[$x];
	 $x++;
	 $meangroup[] = $sum5/$n[$x];
	 $x++;
	 $meangroup[] = $sum6/$n[$x];
	 $x++;
	 $meangroup[] = $sum7/$n[$x];
	 $x++;
	 $meangroup[] = $sum8/$n[$x];
	 $x++;
	 $meangroup[] = (2-($sum9/$n[$x]))*$n[$x];
	 $x++;
	 
	 
	 
	 
	 
	 
	 $lastnamemdq = "SELECT last
	                 FROM mds
					 WHERE initials='{$_SESSION['initials']}'";
	 $lastnamer = mysql_query($lastnamemdq);
	 $lastnamem = mysql_fetch_row($lastnamer);
	 $lastname = trim($lastnamem[0]);

	 
	 
	 $ptid = array();
	 $ptlistq = "SELECT patientid
	             FROM answers
				 WHERE anesthesiologist = '$lastname'";
	 $ptlistr = mysql_query($ptlistq);
	 while ($ptlist = mysql_fetch_row($ptlistr))
	 {
	 	   $ptid[]=$ptlist[0];
		   //echo $ptlist[0].'<br>';
	 }
		   
		   
     $mdq1sum = 0;
	 $mdq2sum = 0;
	 $mdq3sum = 0;
	 $mdq4sum = 0;
	 $mdq5sum = 0;
	 $mdq6sum = 0;
	 $mdq7sum = 0;
	 $mdq8sum = 0;
	 $mdq9sum = 0;
	 $countq1 = 0;
	 $countq2 = 0;
	 $countq3 = 0;
	 $countq4 = 0;
	 $countq5 = 0;
	 $countq6 = 0;
	 $countq7 = 0;
	 $countq8 = 0;
	 $countq9 = 0;
	 
	 foreach($ptid as $value) 
	 {   
   	    $qaq = "SELECT *
		        FROM answers
				WHERE patientid=$value AND questionset=1";
	    $qar = mysql_query($qaq);
		
		while ($qaa = mysql_fetch_row($qar))
		{
		   if ($qaa[3]>0 && $qaa[3]<6)
		   {
		   	  $countq1++;
			  $mdq1sum+=$qaa[3];
		   }
		   if ($qaa[4]>0 && $qaa[4]<6)
		   {
		   	  $countq2++;
			  $mdq2sum+=$qaa[4];
		   } 
		   if ($qaa[5]>0 && $qaa[5]<6)
		   {
		   	  $countq3++;
			  $mdq3sum+=$qaa[5];
		   }
		   if ($qaa[6]>0 && $qaa[6]<6)
		   {
		   	  $countq4++;
			  $mdq4sum+=$qaa[6];
		   }
		   if ($qaa[7]>0 && $qaa[7]<6)
		   {
		   	  $countq5++;
			  $mdq5sum+=$qaa[7];
		   }
		   if ($qaa[8]>0 && $qaa[8]<6)
		   {
		   	  $countq6++;
			  $mdq6sum+=$qaa[8];
		   } 
		   if ($qaa[9]>0 && $qaa[9]<6)
		   {
		   	  $countq7++;
			  $mdq7sum+=$qaa[9];
		   }
		   if ($qaa[10]>0 && $qaa[10]<6)
		   {
		   	  $countq8++;
			  $mdq8sum+=$qaa[10];
		   }
		   if ($qaa[11]>0 && $qaa[11]<3)
		   {
		   	  $countq9++;
			  $mdq9sum+=$qaa[11];
		   }  
		}
     }
	 $mdmean = array();
	 @$mdmean[] = $mdq1sum/$countq1;
	 @$mdmean[] = $mdq2sum/$countq2;
	 @$mdmean[] = $mdq3sum/$countq3;
	 @$mdmean[] = $mdq4sum/$countq4;
	 @$mdmean[] = $mdq5sum/$countq5;
	 @$mdmean[] = $mdq6sum/$countq6;
	 @$mdmean[] = $mdq7sum/$countq7;
	 @$mdmean[] = $mdq8sum/$countq8;
	 @$mdmean[] = (2-($mdq9sum/$countq9))*$countq9;
	 
	 $countq = array($countq1, $countq2, $countq3, $countq4, $countq5, $countq6, $countq7, $countq8, $countq9);

	 
	 echo '
	 	   <center><h3>Anesthesia Responses</h3></center>';
	 calldisplay($meangroup, $mdmean, $n, $countq, 1);
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 unset($n);
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q1>0 AND q1<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q2>0 AND q2<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q3>0 AND q3<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q4>0 AND q4<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q5>0 AND q5<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q6>0 AND q6<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q7>0 AND q7<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers
				WHERE q8>0 AND q8<3 AND questionset=2";
	 $group = mysql_query($groupq);
	 $groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 
	 
	 $sum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers
				WHERE q1>0 AND q1<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum1 += $groupa[0];
	 
	 $sum2 = 0;
	 $groupq = "SELECT q2 
	            FROM answers
				WHERE q2>0 AND q2<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum2 += $groupa[0];	
	 
	 $sum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers
				WHERE q3>0 AND q3<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum3 += $groupa[0];	
	 
	 $sum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers
				WHERE q4>0 AND q4<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum4 += $groupa[0];	  
	 
	 $sum5 = 0;
	 $groupq = "SELECT q5 
	            FROM answers
				WHERE q5>0 AND q5<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum5 += $groupa[0];	
	 
	 $sum6 = 0;
	 $groupq = "SELECT q6 
	            FROM answers
				WHERE q6>0 AND q6<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum6 += $groupa[0];		
	 
	 $sum7 = 0;
	 $groupq = "SELECT q7 
	            FROM answers
				WHERE q7>0 AND q7<6 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum7 += $groupa[0];	 
	 
	 $sum8 = 0;
	 $groupq = "SELECT q8 
	            FROM answers
				WHERE q8>0 AND q8<3 AND questionset=2";
	 $group = mysql_query($groupq);
	 while ($groupa = mysql_fetch_row($group))
	 	   $sum8 += $groupa[0];	 
	 
		   
	 $meangroup2 = array();
	 $x=0;
	 @$meangroup2[] = $sum1/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum2/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum3/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum4/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum5/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum6/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum7/$n[$x];
	 $x++;
	 @$meangroup2[] = (2-($sum8/$n[$x]))*$n[$x];
	 $x++;
	 
	 


		   
     $mdq1sum = 0;
	 $mdq2sum = 0;
	 $mdq3sum = 0;
	 $mdq4sum = 0;
	 $mdq5sum = 0;
	 $mdq6sum = 0;
	 $mdq7sum = 0;
	 $mdq8sum = 0;
	 $mdq9sum = 0;
	 $countq1 = 0;
	 $countq2 = 0;
	 $countq3 = 0;
	 $countq4 = 0;
	 $countq5 = 0;
	 $countq6 = 0;
	 $countq7 = 0;
	 $countq8 = 0;
	 
	 foreach($ptid as $value) 
	 {   
   	    $qaq = "SELECT *
		        FROM answers
				WHERE patientid=$value AND questionset=2";
	    $qar = mysql_query($qaq);
		
		while ($qaa = mysql_fetch_row($qar))
		{
		   if ($qaa[3]>0 && $qaa[3]<6)
		   {
		   	  $countq1++;
			  $mdq1sum+=$qaa[3];
		   }
		   if ($qaa[4]>0 && $qaa[4]<6)
		   {
		   	  $countq2++;
			  $mdq2sum+=$qaa[4];
		   } 
		   if ($qaa[5]>0 && $qaa[5]<6)
		   {
		   	  $countq3++;
			  $mdq3sum+=$qaa[5];
		   }
		   if ($qaa[6]>0 && $qaa[6]<6)
		   {
		   	  $countq4++;
			  $mdq4sum+=$qaa[6];
		   }
		   if ($qaa[7]>0 && $qaa[7]<6)
		   {
		   	  $countq5++;
			  $mdq5sum+=$qaa[7];
		   }
		   if ($qaa[8]>0 && $qaa[8]<6)
		   {
		   	  $countq6++;
			  $mdq6sum+=$qaa[8];
		   } 
		   if ($qaa[9]>0 && $qaa[9]<6)
		   {
		   	  $countq7++;
			  $mdq7sum+=$qaa[9];
		   }
		   if ($qaa[10]>0 && $qaa[10]<3)
		   {
		   	  $countq8++;
			  $mdq8sum+=$qaa[10];
		   }
		}
     }
	 $mdmean2 = array();
	 @$mdmean2[] = $mdq1sum/$countq1;
	 @$mdmean2[] = $mdq2sum/$countq2;
	 @$mdmean2[] = $mdq3sum/$countq3;
	 @$mdmean2[] = $mdq4sum/$countq4;
	 @$mdmean2[] = $mdq5sum/$countq5;
	 @$mdmean2[] = $mdq6sum/$countq6;
	 @$mdmean2[] = $mdq7sum/$countq7;
	 @$mdmean2[] = (2-($mdq8sum/$countq8))*$countq8;
	 
	 unset ($countq);
	 $countq = array($countq1, $countq2, $countq3, $countq4, $countq5, $countq6, $countq7, $countq8);
	 
	 echo '
	 	   <center><h3>Labor Epidural Responses</h3></center>';
	 calldisplay($meangroup2, $mdmean2, $n, $countq, 2);
}
?>
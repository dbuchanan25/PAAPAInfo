<?php
require_once ('../connect2.php');

if (empty ($_REQUEST['mnt']))
{
     echo'
           <form method="post" action="s_a_report.php">
           <table align="center" border="0" width="900" bordercolor="#000000">
              <tr>
		         <td width="300">
				 </td>
		         <td width="300" align="center"><h2>Create Extra Hours Report For Month:</h2>
				 </td>
		         <td width="300">
				 </td>
		      </tr>
		      <tr>
		         <td width="300" align="center">
		         <select name="mnt">
         ';
		 
     for ($x=1; $x<=12; $x++)
     {
        echo "<option>$x</option>\n";
     }
		 
     echo'
                 </select>
		         </td>
		         <td width="300">
				 </td>
		         <td width="300" align="center">
		         <select name="yar">
		 ';
		 
     for ($x=2009; $x<=2010; $x++)
     {
        echo "<option>$x</option>\n";
     }
		 
     echo'
                 </select>
		         </td>
		      </tr>
           </table>
		 ';
		 
     echo'
		   <table align="center" border="0" width="1000" bordercolor="#000000">
              <tr>
			  </tr>
              <tr>
			     <td>
				 </td>
				 <td align="center">
                 <input type="submit" name="submit" value="Submit" 
				                     style="width:200px; height:30px">
	             </td>
              </tr>
           </table>
         ';
}

else
{   

   echo '<table align="left" border="1">';
   
   
   $ind = $_REQUEST['mnt'];
   $indexyear = $_REQUEST['yar'];
   
   
   for ($ix = 0; $ix < 12; $ix++)
   {
	   if ($ind>12)
	   {
	   		$ind = 1;
	   		$indexyear++;
	   }
	   $dinmo = cal_days_in_month(CAL_GREGORIAN, $ind, $indexyear);
	   $s = "SELECT * from monthassignment 
	         WHERE monthnumber=$ind 
	         AND yearnumber=$indexyear
AND (assignment='Spk 2')
	         ORDER BY yearnumber, monthnumber, daynumber";
	         //AND (assignment='C OR')
	         //AND (assignment='CallWE2')
	         //AND (assignment='Heart Call')
	   		 //AND (assignment='Peds Call' || assignment='Peds Call Short')
	         //AND (assignment='S OH')
	         //AND (assignment='Monroe')
	         //AND (assignment='Pain')
	         //AND (assignment='Pain2')
	         //AND (assignment='PainWE')
	         //AND (assignment='BK OH')
	         //AND (assignment='S OH2')
	         //AND (assignment='PrSrg')
	         //AND (assignment='Balnt')
	         //AND (assignment='SI OH')
	         //AND (assignment='Sds 1')
	         //AND (assignment='W 1')
	   	     //AND (assignment='OB 1')
	         //AND (assignment='ORMGR')
	   	     //AND (assignment='Smat2')
	         //AND (assignment='S Mat')
	   		 //AND (assignment='Spk')
	         //AND (assignment='Ops 2')
	         //AND (assignment='Ops 1')
	         //AND (assignment='S Rad')
	         //AND (assignment='S Eye')
	         //AND (assignment='S C')
	         //AND (assignment='S B') 
	         //AND (assignment='S A')
	         //AND (assignment='Ortho Call')
	         //AND (assignment='H 2')
	   $sq = mysql_query($s);
	   while ($sr = mysql_fetch_row($sq))
	   {
	      /*$nextdayi = $sr[2]+1;
	      $indexyeari=$indexyear;
	      $indi=$ind;
	      if ($nextdayi>$dinmo)
	      {
	         $nextdayi = 1;
	         $indi += 1;
		      if ($indi>12)
			  {
			   		$indi = 1;
			   		$indexyeari=$indexyear+1;
			  }
	      }*/
	   
	      $indi=$ind;
	      $nextdayi=$sr[2];
	      $indexyeari=$indexyear;
	      
	    
	      $ss = "SELECT * from monthassignment 
	             WHERE mdnumber={$sr[0]} 
	             AND monthnumber=$indi 
	             AND daynumber=$nextdayi 
	             AND yearnumber={$sr[3]} 
	             AND assigntype=4";
		  $ssq = mysql_query($ss); 

	      while ($ssr = mysql_fetch_row($ssq))
	      { 
		      if (
		          (strcmp(trim($ssr[4]),'OrOff')==0) || 
		          (strcmp(trim($ssr[4]),'M Off')==0) || 
		          (strcmp(trim($ssr[4]),'H Off')==0) || 
		          (strcmp(trim($ssr[4]),'Wkend')==0) || 
		          (strcmp(trim($ssr[4]),'Vac  ')==0) || 
		          (strcmp(trim($ssr[4]),'Vac')==0) || 
		          (strcmp(trim($ssr[4]),'Off')==0) || 
		          (strcmp(trim($ssr[4]),'Hlday')==0) || 
		          (strcmp(trim($ssr[4]),'S Bus')==0) || 
		          (strcmp(trim($ssr[4]),'PainWE')==0)||
		          (strcmp(trim($ssr[4]),'OhOff')==0)||
		          (strcmp(trim($ssr[4]),'ObOff')==0)

		         )
		      {
		      }
		      else
		      {   
		         echo '
		  	     <tr>
		  	     <td>'.$sr[0].'</td><td>'.$indi.'</td><td>'.$nextdayi.'</td><td>'.$indexyeari.'</td><td>'.$ssr[4].
		  	     '</td><td>'.$ssr[6].'</td><td>'.$ssr[7].'</td><td>'.$ssr[8].'</td><td>'.$ssr[9].
		  	     '</td><td>'.$ssr[10].'</td><td>'.$sr[4].'</td></tr>';
		      }
		  }
	   }
	   $ind++;	   
   }  
   echo '</table>';
}
?>

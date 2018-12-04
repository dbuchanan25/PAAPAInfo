<?php
require_once ('connect2.php');

if (empty ($_REQUEST['mnt']))
{
     echo'
           <form method="post" action="ahreport.php">
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
		 
     for ($x=2008; $x<=2012; $x++)
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
   $s = "SELECT * from monthassignment where monthnumber='{$_REQUEST['mnt']}' AND yearnumber='{$_REQUEST['yar']}' AND assigntype=4";
   $sq = mysql_query($s);
   
   echo '<table align="left" border="1">
   
       <h1 align="center">
	   Added Hours Report For: '.$_REQUEST["mnt"].'/'.$_REQUEST["yar"].'
	   </h1>';
   
   while ($sr = mysql_fetch_row($sq))
   {
     echo '
	   <tr>
	   <td>'.$sr[0].'</td><td>'.$sr[1].'</td><td>'.$sr[2].'</td><td>'.$sr[3].'</td><td>'.$sr[4].'</td>
	   <td>'.$sr[5].'</td><td>'.$sr[6].'</td><td>'.$sr[7].'</td><td>'.$sr[8].'</td><td>'.$sr[9].'</td>
	   <td>'.$sr[10].'</td>
	   ';  
   
   $hs = "SELECT * FROM monthassignment WHERE mdnumber={$sr[0]} AND monthnumber={$sr[1]} AND daynumber={$sr[2]} AND yearnumber={$_REQUEST['yar']} AND assigntype=1";
   $hsq = mysql_query($hs);
   
   while ($hsqr = mysql_fetch_row($hsq))
   {
      echo '<td>'.$hsqr[4].'</td>';
   }
	  
   echo '</tr>';
   }
	     
   
   
   echo '</table>';

   
/*
   echo '
      <h1 align="center">
	  Pay Report For: '.$_REQUEST["mnt"].'/'.$_REQUEST["yar"].'
	  </h1>
	  
      <table align="center" border="1">
	     <tr>
	        <td width="50" align="center">
	        Number
			</td>
			<td width="100" align="center">
			Name
			</td>
	        <td width="50" align="center">
			Initials
			</td>
			<td width="100" align="center">
			Pay Rate
			</td>
			<td width="100" align="center">
			Administrative Days
			</td>
	        <td width="100" align="center">
			Gross Hours
			</td>
			<td width="100" align="center">
			Net Hours
			</td>
			<td width="100" align="center">
			Pay Fraction
			</td>
			<td width="100" align="center">
			Shifts
			</td>
         </tr>
	 ';

   $counter=0;
   $pay[$counter]=0.0;
   $sqlq = "SELECT DISTINCT number, last, initials, payfraction, admin FROM mds ORDER by number";
   $sqlqu = mysql_query($sqlq);
   while ($sqlmd = mysql_fetch_row($sqlqu))
   {
     echo '
	       <tr>
	          <td width="50" align="center">'.
			  $sqlmd[0].'
			  </td>
			  <td width="100" align="center">'.
			  $sqlmd[1].'
			  </td>
			  <td width="50" align="center">'.
			  $sqlmd[2].'
			  </td>
			  <td width="100" align="center">'.
			  $sqlmd[3].'
			  </td>
			  <td width="100" align="center">'.
			  $sqlmd[4].'
			  </td>';
	 
     $sqlq1 = "SELECT assignment, weekend, beginblock, endblock, assigntype FROM 
	           monthassignment WHERE monthnumber={$_REQUEST['mnt']} and 
			   yearnumber={$_REQUEST['yar']} and mdnumber=$sqlmd[0]";
	 $sqlq2 = mysql_query($sqlq1);
	 
	 while ($sqlq3 = mysql_fetch_row($sqlq2))
	 {
	    if ($sqlq3[4]==1 || $sqlq3[4]==2)
		{
	     $sq1 = "SELECT payincrement FROM assignments where assignment='$sqlq3[0]' and 
		         weekend=$sqlq3[1] and home=0 and addhours=0";
		 $sq2 = mysql_query($sq1);
		 $sq3 = mysql_fetch_row($sq2);
		 $pay[$counter] += (($sqlq3[3]-$sqlq3[2])*$sq3[0]);
		}
		else if ($sqlq3[4]==3)
		{
	     $sq1 = "SELECT payincrement FROM assignments where assignment='$sqlq3[0]' and 
		         weekend=$sqlq3[1] and home=1 and addhours=0";
		 $sq2 = mysql_query($sq1);
		 $sq3 = mysql_fetch_row($sq2);
		 $pay[$counter] += (($sqlq3[3]-$sqlq3[2])*$sq3[0]);
		}
		else if ($sqlq3[4]==4)
		{
	     $sq1 = "SELECT payincrement FROM assignments where assignment='$sqlq3[0]' and 
		         weekend=$sqlq3[1] and home=0 and addhours=1";
		 $sq2 = mysql_query($sq1);
		 $sq3 = mysql_fetch_row($sq2);
		 $pay[$counter] += (($sqlq3[3]-$sqlq3[2])*$sq3[0]);
		}	 
	 }
   $pay[$counter] += ($sqlmd[4]*6.75);
   $grosspay[$counter] = $pay[$counter];
   $grosspay_formatted = number_format($grosspay[$counter], 6, '.',',');
   $pay[$counter]*=$sqlmd[3];
   $pay_number = number_format($pay[$counter], 6, '.', ',');
   $total_pay_fraction = $pay[$counter]/$totalpay;
   $tpf_formatted = number_format($total_pay_fraction, 6, '.',',');
   $shifts = $pay_number/9;
   $shifts_formatted = number_format($shifts, 6, '.', ',');

   echo '
              <td width="100" align="right">'.
		      $grosspay_formatted.'
		      </td>
		      <td width="100" align="right">'.
		      $pay_number.'
		      </td>
		      <td width="100" align="right">'.
		      $tpf_formatted.'
		      </td>
			  <td width="100" align="right">'.
		      $shifts_formatted.'
		      </td>
		   </tr>
	 ';
   $counter++;
   $pay[$counter]=0.0;
   }
   echo'
        </table>
		<table align="center">
           <tr>
              <td>
		      Net Total Hours For Month = '.number_format($totalpay,4,".",",").'
		      </td>
           </tr>
        </table>
	   ';
	   */
}
?>


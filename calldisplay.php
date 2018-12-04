<?php
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-07
 */
function calldisplay($meangroup, $mdmean, $n, $countq, $set)
{
  echo '<table align="center">
           <tr>
              <td width="18%"></td>
              <td align="center" width="48%" style = "border:1px solid black;"><b>QUESTIONS - SCORED FROM 1 (WORST) TO 5 (BEST)</b>
              </td>
              <td align="center" width="4%" style = "border:1px solid black;"><b>Group n</b></td>
              <td align="center" width="4%" style = "border:1px solid black;"><b>Group Mean</b></td>
              <td align="center" width="4%" style = "border:1px solid black;"><b>Your n</b></td>
              <td align="center" width="4%" style = "border:1px solid black;"><b>Your Mean</b></td>
              <td width="18%"></td>
           </tr>';
  
  
  $questionq = "SELECT questionstring
                FROM questions
                WHERE questionset = $set
                ORDER BY questionnumber";
  $questionr = mysql_query($questionq);
  
  $q = 0;
  $qn = 1;
  
  while ($questiona = mysql_fetch_row($questionr))
  {
   		   if (($qn == 9 AND $set==1) OR ($qn==8 AND $set==2))
		   {
		   echo'
		   <tr>
                           <td width="18%"></td>
		   	   <td align="center" width="48%" style = "border:1px solid black;"><b>
			                   THE FOLLOWING QUESTION\'S RESULTS ARE IN ABSOLUTE NUMBERS.</b></td>
			   <td align="center" width="4%" style = "border:1px solid black;"><b>Group n</b></td>
			   <td align="center" width="4%" style = "border:1px solid black;"><b># YES</b></td>
			   <td align="center" width="4%" style = "border:1px solid black;"><b>Your n</b></td>
			   <td align="center" width="4%" style = "border:1px solid black;"><b># YES</b></td>
                           <td width="18%"></td>
		   </tr>
                   <tr>
                           <td width="18%"></td>
		   	   <td align="left" width="48%" style = "border:1px solid black;">'.$qn.'. '.$questiona[0].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.$n[$q].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.number_format($meangroup[$q],0).'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.$countq[$q].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.number_format($mdmean[$q],0).'</td>
                           <td width="18%"></td>
		   </tr>';
		   $q++;
		   $qn++;
		   }
		   else
		   {		   
   		   echo'
                   <tr>
                           <td width="18%"></td>
		   	   <td align="left" width="48%" style = "border:1px solid black;">'.$qn.'. '.$questiona[0].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.$n[$q].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.number_format($meangroup[$q],3).'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.$countq[$q].'</td>
			   <td align="center" width="4%" style = "border:1px solid black;">'.number_format($mdmean[$q],3).'</td>
                           <td width="18%"></td>
		   </tr>';
		   $q++;
		   $qn++;
		   }
  }  
  echo'
  	    </table>';
  return;
}
		
?>
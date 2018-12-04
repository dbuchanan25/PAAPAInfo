<?php

function arch($numericaldayofmonth, $mno)
{
	 $inq = "SELECT anesthesiologistinitials, confirmation, priority, credited 
	 		 FROM vacation 
	 		 WHERE month=$mno 
	 		       AND day=$numericaldayofmonth+1 
	 		       AND year={$_SESSION['yr']} 
	 		 ORDER BY priority";
     $inqinfo = mysql_query($inq);
     $num=mysql_numrows($inqinfo);  
     for ($x=0; $x<$num; $x++)
       $vacinfo[$x] = mysql_fetch_array($inqinfo, MYSQL_NUM);
       
     $specialrequestq = "SELECT * 
                         FROM request
                         WHERE month=$mno 
                               AND day=$numericaldayofmonth+1 
                               AND year={$_SESSION['yr']}
                               AND mdnumber={$_SESSION['schedmdnum']}";
     $srquery = mysql_query($specialrequestq);
     if (@mysql_numrows($srquery)>=1)
     {
     	$sr = TRUE;
     }
     
     
     $posavailq = "SELECT *
                   FROM calendar2011
                   WHERE month=$mno
                   AND day=$numericaldayofmonth+1";
     $posavailquery = mysql_query($posavailq);
     $posavail = mysql_fetch_row($posavailquery);
     $creditedavail = $posavail[3]+$posavail[4];
     $uncreditedavail = $posavail[5]+$posavail[6];


	 echo'
	 <td bgcolor="#ffeeee" width="150" height="85" align="center">
	    <table rules=none frame=border bordercolor=black border=1>
	    <tr>
	    <td>
        <table border="0" cellspacing="0" cellpadding="0" style="color:black">
		  <tr>
		   <td bgcolor="#ffffff" height="19" width="148" align="left" style="font-size:small">
		   '.++$numericaldayofmonth.'   
		   </td>
          </tr>
        </table>
        <table style="font-size:10">
        <LINK REL=StyleSheet HREF="style1.css" TYPE="text/css">
		  <tr>';
	 
		for ($y=0; $y<4; $y++)
		{	 
			for ($x=0+$y; $x<16; $x=$x+4)
			{	  
				    if ($_SESSION['initials']==$vacinfo[$x][0])
					{
				    	if ($vacinfo[$x][3]==-1)
				    		echo'<td class="md" bgcolor=#989898 height="16" width="37" align="center">';
				    	else if ($vacinfo[$x][3]==0)
				    		echo'<td class="md" bgcolor=#E8E8E8 height="16" width="37" align="center"><b>';
				    	else if ($vacinfo[$x][3]==5)
				    		echo'<td class="md" bgcolor=#FFFFFF height="16" width="37" align="center"><b>';
				    	else 
				    		echo'<td class="md" bgcolor=#FFFFFF height="16" width="37" align="center"><b>';
				    }
				    else if ($x < $creditedavail && $creditedavail!=0)
				    {
				       echo'<td bgcolor="#ffffff" height="16" width="37" align="center">';
				    }
					else if ($x < $uncreditedavail+$creditedavail
					         && (($x >= $creditedavail)|| $creditedavail==0))
				    {
				       echo'<td bgcolor=#E8E8E8  height="16" width="37" align="center">';
				    }	  
				    else
				       echo'<td bgcolor=#989898 height="16" width="37" align="center">';
				    echo 
					   $vacinfo[$x][0].'
					   </font></td>';
			}
				  echo' 
		          </tr>
		          <tr>';
		}	   
		echo' 
		</table>
		</td>
		</tr>
		</table>
	 </td>';
   } 
   $vacinfo=NULL;
?>
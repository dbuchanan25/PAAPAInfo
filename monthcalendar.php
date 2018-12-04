<?php
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-07
 */
function monthcalendar($numericaldayofmonth, $mda)
  {
	 $domo = $numericaldayofmonth+1;
	 $monthassignpri = "    SELECT assignment, beginblock, endblock, weekend
                                FROM monthassignment
                                WHERE daynumber=$domo
                                AND yearnumber={$_SESSION['dty']}
                                AND monthnumber={$_SESSION['dtm']}
                                AND assigntype=1 and mdnumber=$mda";
        $rpri2 = mysql_query($monthassignpri);
	 
	 if (!empty($rpri2))
	 {
	    $pri = mysql_fetch_row($rpri2);
            $normalpri = "  SELECT beginblock,endblock
                            FROM assignments
                            WHERE assignment='$pri[0]'
                            AND weekend=$pri[3]";
	    $norpri = mysql_query($normalpri);
	    $npri = mysql_fetch_row($norpri);
	 
	    if ($npri[0]!=$pri[1] || $npri[1]!=$pri[2])
	       $prihrs=(($pri[2]-$pri[1])-($npri[1]-$npri[0]))/4;
	 }
	 else
	 {
	    echo $monthassignpri;
	 }

	 $monthassignsec = "SELECT assignment, beginblock, endblock
                            FROM monthassignment
                            WHERE daynumber=$domo
                            AND yearnumber={$_SESSION['dty']}
                            AND monthnumber={$_SESSION['dtm']}
                            AND assigntype=2 and mdnumber=$mda order by daynumber";
     $rsec2 = mysql_query($monthassignsec);
	 if (!empty($rsec2))
	 {
	    $sec = mysql_fetch_row($rsec2);
	 
	    $normalsec = "select beginblock,endblock from assignments where assignment='$sec[0]' and weekend=$pri[3]";
	    $norsec = mysql_query($normalsec);
	    $nsec = mysql_fetch_row($norsec);
	 
	    if ($nsec[0]!=$sec[1] || $nsec[1]!=$sec[2])
	       $sechrs=(($sec[2]-$sec[1])-($nsec[1]-$nsec[0]))/4;
	 }

	 $monthassignhome = "select assignment, beginblock, endblock from monthassignment where daynumber=$domo and yearnumber={$_SESSION['dty']} and monthnumber={$_SESSION['dtm']} and assigntype=3 and mdnumber=$mda";
     $rhome2 = mysql_query($monthassignhome);
	 if (!empty($rhome2))
	 {
	    $home = mysql_fetch_row($rhome2);
	 
	    $normalhome = "select beginblock,endblock from assignments where assignment='$home[0]' and weekend=$pri[3]";
	    $norhome = mysql_query($normalhome);
	    $nhome = mysql_fetch_row($norhome);
	 
	    if ($nhome[0]!=$home[1] || $nhome[1]!=$home[2])
	       $homehrs=(($home[2]-$home[1])-($nhome[1]-$nhome[0]))/4;
	 }


	 $monthassignah = "select beginblock, endblock from monthassignment where daynumber=$domo and yearnumber={$_SESSION['dty']} and monthnumber={$_SESSION['dtm']} and assigntype=4 and mdnumber=$mda";
     $rah2 = mysql_query($monthassignah);
	 $adhrs = 0;
	 while ($ahrs = mysql_fetch_row($rah2))
	    $adhrs+=(($ahrs[1]-$ahrs[0])/4);

	 echo '<td bgcolor="white" width="100" height="50" align="center">';

	 echo '<table border="0" cellspacing="0" cellpadding="0">
           <tr>';
		   if ($pri[3]==0)
		     echo '<td height="12" bgcolor="white" width="98" align="center" style="font-size:small;"><b>';
		   else
		     echo '<td height="12" bgcolor="yellow" width="98" align="center" style="font-size:small;"><b>';
		   if ($prihrs==0)
		   {
		   echo
		   $pri[0]
		   ;
		   }
		   else
		   {
		   echo
		   $pri[0].' ('.number_format($prihrs,2,'.',',').')'
		   ;
		   }
		   echo'
		   </b>
		   </td>
          </tr>
		  <tr>';
		   if ($pri[3]==0)
		     echo '<td height="12" bgcolor="white" width="98" align="center" style="font-size:smaller;">';
		   else
		     echo '<td height="12" bgcolor="yellow" width="98" align="center" style="font-size:smaller;">';
		   if ($sechrs==0)
		   {
		   echo
		   $sec[0]
		   ;
		   }
		   else
		   {
		   echo
		   $sec[0].' ('.number_format($sechrs,2,'.',',').')'
		   ;
		   }
		   echo'
		   </td>
           </tr>
		   <tr>';
		   if ($pri[3]==0)
		     echo '<td height="12" bgcolor="white" width="98" align="center" style="font-size:smaller;">';
		   else
		     echo '<td height="12" bgcolor="yellow" width="98" align="center" style="font-size:smaller;">';
		   if ($homehrs==0)
		   {
		   echo
		   $home[0]
		   ;
		   }
		   else
		   {
		   echo
		   $home[0].' ('.number_format($homehrs,2,'.',',').')'
		   ;
		   }
		   echo'
		   </td>
          </tr>';
		  if ($adhrs==0)
		  {
		  echo'
		  <tr>';
		   if ($pri[3]==0)
		     echo '<td height="12" bgcolor="white" width="98" align="center" style="font-size:x-small;">';
		   else
		     echo '<td height="12" bgcolor="yellow" width="98" align="center" style="font-size:x-small;">';
		  echo'
		   </td>
          </tr>
		  ';
		  }
		  else
		  {
		  echo'
		  <tr>';
		   if ($pri[3]==0)
		     echo '<td height="12" bgcolor="white" width="98" align="center" style="font-size:x-small;">';
		   else
		     echo '<td height="12" bgcolor="yellow" width="98" align="center" style="font-size:x-small;">';
		   echo'
		   Added Hours ('.number_format($adhrs,2,'.',',').')
		   </td>
          </tr>
		  ';
		  }
		  echo'
		</table>
	 </td>';
   } 
?>
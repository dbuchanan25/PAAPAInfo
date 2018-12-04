<?php
require_once ('../connect2.php');

if (empty ($_REQUEST['mnt']))
{
     echo'
           <form method="post" action="pain_addedhours.php">
           <table align="center" border="0" width="900" bordercolor="#000000">
              <tr>
		         <td width="300">
				 </td>
		         <td width="300" align="center"><h2>Create Pain Extra Hours Report For Month:</h2>
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
		 
     for ($x=2008; $x<=2010; $x++)
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

   echo '<table align="left" border="1">
   
   <h1 align="center">
	Pain Added Hours Report For: '.$_REQUEST["mnt"].'/'.$_REQUEST["yar"].'
   </h1>';
   
   $s = "SELECT * from monthassignment where monthnumber={$_REQUEST['mnt']} AND yearnumber={$_REQUEST['yar']} AND (assignment='Pain')";
   $sq = mysql_query($s);
   while ($sr = mysql_fetch_row($sq))
   {	    
      $ss = "SELECT * from monthassignment where mdnumber={$sr[0]} AND monthnumber={$sr[1]} AND daynumber={$sr[2]} AND yearnumber={$sr[3]} AND assigntype=4 AND assignment='Assignment Overrun'";
	  $ssq = mysql_query($ss); 
      while ($ssr = mysql_fetch_row($ssq))
      {    
         echo '
  	     <tr>
  	     <td>'.$sr[0].'</td><td>'.$sr[1].'</td><td>'.$sr[2].'</td><td>'.$sr[3].'</td><td>'.$sr[4].'</td><td>'.$ssr[6].'</td><td>'.$ssr[8].'</td></tr>';
      }
   }  
   echo '</table>';
}
?>

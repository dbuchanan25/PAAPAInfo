<?php # Script 11.1 - gettimessec.php

   $q=$_GET["q"];

   $con = @mysql_connect('localhost', 'paapaus_dcb', 'srt101');

    if ($con)
       $db_selected = @mysql_select_db("paapaus_anesthesiapay", $con);
    if (!isset($db_selected))
    {
        //LOCAL CONNECTION
        $conlocal = @mysql_connect('localhost', 'root', '');
        if ($conlocal)
            $db_selected_local = @mysql_select_db("anesthesiapay", $conlocal);
        else if (!isset($db_selected) && !isset($db_selected_local))
        {
            die('Could not connect: ' . mysql_error());
        }
    }
   
   $sqlstatement=("select beginblock, endblock from assignments where n=$q");

   $sql=mysql_query($sqlstatement);
   $sqlf = mysql_fetch_row($sql);
   

   $tr1=mysql_query("Select time from timeperiods where timeperiod>=$sqlf[0] and timeperiod<$sqlf[1]");
   $tr2=mysql_query("Select time from timeperiods where timeperiod>$sqlf[0]+1 and timeperiod<=$sqlf[1]");  
 
$e = mysql_query("select time from timeperiods where timeperiod=$sqlf[1]");
if (!empty($e))
   $end = mysql_fetch_row($e);

   
echo'	
      <td align="left" width="683">
	  <table border="0">
	  <td align="right" height="25px" width="100px" style="color:black">
	  Begin Time:
	  </td>
	  <td align="center" height="25px" width="100px" style="color:black">	   
	  <select name="btsecondary">
	  ';
	  
while($row2 = mysql_fetch_row($tr1))
{
    echo "<option>$row2[0]</option>\n";
}

echo '</select>
      </td>
      <td align="right" height="25" width="100" style="color:black">
	  End Time:
	  </td>
	  <td align="center" height="25" width="100" style="color:black">
      <select name="etsecondary">
	  ';
	    
while($row3 = mysql_fetch_row($tr2))
{
  if (trim($row3[0])==trim($end[0]))
    echo "<option selected='selected'>$row3[0]</option>\n";
  else
    echo "<option>$row3[0]</option>\n";
}

echo '</select>
      </td>
	  </table>
      </td>';
mysql_close($con);
?>
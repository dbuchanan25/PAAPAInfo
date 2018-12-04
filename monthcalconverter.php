<?php
/*
 * Version 02_02
 */
/*
 * Last Revise: 2014-01-17
 * Revised 2012-11-19
 */
/*
 * Updated 2014-01-17 to enter the beginning time and ending time for all
 * assignments into monthcal instead of added or subtracted hours
 */
   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   $con = @mysql_connect('localhost', 'paapaus_dcb', 'srt101');

   if (!$con)
   {
       //LOCAL CONNECTION
       $con = @mysql_connect('localhost', 'root', '');
       if (!$con)
       {
           die('Could not connect: ' . mysql_error());
       }
       else
       {
           //LOCAL CONNECTION
           mysql_select_db("anesthesiapay", $con);
       }
   }
   else
   {
       ///////////////////////////////////////////////////////////////////////////////////////////
       //WEBSITE CONNECTION
       mysql_select_db("paapaus_anesthesiapay", $con);
   }


$mc = date('n');
if ($mc == 12)
    $ma = 1;
else
    $ma = $mc+1;

$yc = date('Y');
if ($mc == 12)
    $ya = $yc+1;
else
    $ya = $yc;

$s = "TRUNCATE TABLE monthcal";
mysql_query($s);


/*
 * This SELECT statement gets the assignments for this month and next month
 * to populate the table 'monthcal'.
 */
$sql = "SELECT  mdnumber,
                monthnumber,
                daynumber,
                yearnumber,
                assignment,
                assigntype,
                bt,
                et,
                weekend
        FROM monthassignment
        WHERE (yearnumber = ".$yc." AND monthnumber = ".$mc.")
           OR (yearnumber = ".$ya." AND monthnumber = ".$ma.")
        ORDER BY mdnumber, daynumber, assigntype, bt";
$sqlq = mysql_query($sql);

while ($sqla = @mysql_fetch_array($sqlq))
{
        $newsql = "  INSERT INTO monthcal
                     VALUES({$sqla['mdnumber']},
                            {$sqla['monthnumber']},
                            {$sqla['daynumber']},
                            {$sqla['yearnumber']},
                            '{$sqla['assignment']}',
                            {$sqla['assigntype']},
                            {$sqla['weekend']},
                            '{$sqla['bt']}',
                            '{$sqla['et']}')";
        mysql_query($newsql);
}
   echo "MONTHCALCONVERTER has completed.";
?>

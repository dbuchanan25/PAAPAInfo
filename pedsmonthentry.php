<?php
/*
 * Version 02_02
 */
 /*
  * Last Revised: 2015-05-10
  * Revised: 2014-01-18
  * Revised:  2011-08-06
  */
/*
 * Revised 2015-05-10 to correct an error in a monthcal assignment statement
 * for when codenumber=1
 */
/*
 * Revised 2014-01-18 to acurately enter data into the new version of 'monthcal'
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
   
   
echo "<script type=\"text/javascript\">";
echo "alert('If using this page for the same month, there are lines that need alteration in the code.')";
echo "</script>";

$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
///OMG////////////////////////////////////////////////////////////////////////////////////////////
//Use the next line if you are doing the next month, not the present month
$datetime->modify("+30 days");
$month = $datetime->format('n');
$monthnext = $month;
$year = $datetime->format('Y');

if ($monthnext == 13)
{
	$monthnext = 1;
	$year = $year+1;
}

$pedsq = "SELECT *
          FROM pedsmonth";
$pedsquery = mysql_query($pedsq);
$cr = TRUE;

/*
 * pedsmonth fields
 * [0] mdnumber
 * [1] day
 * [2] codenumber
 */
while ($peds = @mysql_fetch_array($pedsquery))
{
/*
 * $peds[2]
 * 0=weekday
 * 1=weekday call
 * 2=weekend/holiday call
 * 3=weekday call short
 * 4=CWkd2/Peds for weekend
 */
    
	if ($peds['codenumber']==1)
	{
                $insertp2 = "INSERT INTO monthcal
                               VALUES
                               (
                                   {$peds['mdnumber']},
                                   $monthnext,
                                   {$peds['day']},
                                   $year,
                                   'Peds Call',
                                   3,
                                   0,
                                   '14:30',
                                   '06:30'
                                )
                              ";
                echo $insertp2.'<br>';              
		$pedsmonthcalinsert = mysql_query($insertp2);
                                                       
                $insertp = "INSERT INTO monthassignment
		                             VALUES
		                             (
                                                {$peds['mdnumber']},
                                                $monthnext,
                                                {$peds['day']},
                                                $year,
                                                 'Peds Call',
                                                 3,
                                                 '14:30',
                                                 34,
                                                 '06:30',
                                                 98,
                                                 0,
                                                 now(),
                                                 NULL,
                                                 NULL
                                              )";
                $pedsmainsert = mysql_query($insertp);                                       
      	}
        else if ($peds['codenumber']==0)
	{
                $getCurrentAssignmentStatement = "  SELECT assignment, counter
                                                    FROM monthassignment
                                                    WHERE monthnumber=$monthnext
                                                    AND daynumber={$peds['day']}
                                                    AND yearnumber=$year
                                                    AND mdnumber={$peds['mdnumber']}
                                                    AND assigntype=1";

                $getCurrentAssignmentQuery = mysql_query($getCurrentAssignmentStatement);
                $getCurrentAssignmentResult = mysql_fetch_array($getCurrentAssignmentQuery);

                $assig = trim($getCurrentAssignmentResult['assignment']);
                $assig .= "/Peds";
                echo $assig.'<br>';
                $checkPedsAssignment = "SELECT *
                                        FROM assignments
                                        WHERE assignment LIKE '$assig'";
                $checkq = mysql_query($checkPedsAssignment);
                if (mysql_num_rows($checkq)==1)
                {
                    $q = "UPDATE monthassignment
                          SET assignment='$assig'
                          WHERE counter={$getCurrentAssignmentResult['counter']}";
                    mysql_query($q);

                    mysql_query("UPDATE monthcal
                                 SET assignment='$assig'
                                 WHERE monthnumber=$monthnext
                                 AND daynumber={$peds['day']}
                                 AND yearnumber=$year
                                 AND mdnumber={$peds['mdnumber']}
                                 AND assigntype=1");
                }
                else
                {
                    echo 'An error occurred for counter number: '.
                            $getCurrentAssignmentResult['counter'].' for day '.
                            $peds['day'].'.<br>';
                    echo $assig.'<br>';
                    while ($res = mysql_fetch_array($checkq))
                        echo $res['assignment'];
                }
                                
	}
	else if ($peds['codenumber']==3)
	{
		$pedsmonthcalinsert = mysql_query("INSERT INTO monthcal
		                                   VALUES
		                                   (
                                                        {$peds['mdnumber']},
                                                        $monthnext,
                                                        {$peds['day']},
                                                        $year,
                                                        'Peds Call Short',
                                                        3,
                                                        0,
                                                        '13:00',
                                                        '06:30'
                                                    )
                                                  ");
		$pedsmainsert = mysql_query("INSERT INTO monthassignment
		                             VALUES
		                             (
                                                {$peds['mdnumber']},
                                                $monthnext,
                                                {$peds['day']},
                                                $year,
                                                 'Peds Call Short',
                                                 3,
                                                 '13:00',
                                                 28,
                                                 '06:30',
                                                 98,
                                                 0,
                                                 now(),
                                                 NULL,
                                                 NULL
                                              )
                                            ");
	}
	else if ($peds['codenumber']==2)
	{
		$pedsmonthcalinsert = mysql_query("INSERT INTO monthcal
		                                   VALUES
		                                   (
                                                       {$peds['mdnumber']},
                                                       $monthnext,
                                                       {$peds['day']},
                                                       $year,
                                                       'Peds Call',
                                                       3,
                                                       1,
                                                       '07:00',
                                                       '07:00'
                                                   )
                                                 ");
		$pedsmainsert = mysql_query("INSERT INTO monthassignment
		                             VALUES
		                             (
                                                 {$peds['mdnumber']},
                                                 $monthnext,
                                                 {$peds['day']},
                                                 $year,
                                                 'Peds Call',
                                                 3,
                                                 '07:00',
                                                 4,
                                                 '07:00',
                                                 100,
                                                 1,
                                                 now(),
                                                 NULL,
                                                 NULL
                                             )
                                           ");
	}

        else if ($peds['codenumber']==4)
	{
                $cwmonthcaldelete = mysql_query("DELETE FROM monthcal
                        WHERE mdnumber      = {$peds['mdnumber']}
                        AND   daynumber     = {$peds['day']}
                        AND   monthnumber   = $monthnext
                        AND   yearnumber    = $year
                        AND   assignment LIKE '%CWkn2%'");

		$pedsmonthcalinsert = mysql_query("INSERT INTO monthcal
		                                   VALUES
		                                   (
                                                       {$peds['mdnumber']},
                                                       $monthnext,
                                                       {$peds['day']},
                                                       $year,
                                                       'Peds Call/CWkn2',
                                                       3,
                                                       1,
                                                       '07:00',
                                                       '07:00'
                                                   )
                                                 ");
                $cwmaindelete = mysql_query("DELETE FROM monthassignment
                        WHERE mdnumber      = {$peds['mdnumber']}
                        AND   daynumber     = {$peds['day']}
                        AND   monthnumber   = $monthnext
                        AND   yearnumber    = $year
                        AND   assignment LIKE '%CWkn2%'");
		$pedsmainsert = mysql_query("INSERT INTO monthassignment
		                             VALUES
		                             (
                                                 {$peds['mdnumber']},
                                                 $monthnext,
                                                 {$peds['day']},
                                                 $year,
                                                 'Peds Call/CWkn2',
                                                 3,
                                                 '07:00',
                                                 4,
                                                 '07:00',
                                                 100,
                                                 1,
                                                 now(),
                                                 NULL,
                                                 NULL
                                             )
                                           ");
	}
	else
	{
		echo 'There was a problem entering the data: '.$peds['mdnumber'].' 
                    on '.$peds['day'];
		$cr=FALSE;
	}	                                                 	
}
if ($cr)
{
	echo 'All pediatric call data entered.';
}
?>
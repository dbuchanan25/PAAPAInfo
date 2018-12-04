<?php
/*
 * Version 02_01
 */
 /*
 * Last Revised:  2012-04-27
 */
///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   $con = mysql_connect('localhost', 'paapaus_dcb', 'srt101');
   //LOCAL CONNECTION
   //$con = mysql_connect('localhost', 'root', 'srt101');
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }


   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   mysql_select_db("paapaus_anesthesiapay", $con);
   //LOCAL CONNECTION
   //mysql_select_db("anesthesiapay", $con);


$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
///OMG////////////////////////////////////////////////////////////////////////////////////////////

$month = $datetime->format('n') + 1;
$year = $datetime->format('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year) ;


$extrasq = "SELECT *
            FROM extra";
$extraquery = mysql_query($extrasq);
$cr = TRUE;

while ($extra = mysql_fetch_row($extraquery))
{
    for ($x = 0; $x < $daysInMonth; $x++)
    {
        $datetime->setDate($year, $month, $x+1);
        $dow = $datetime->format('N');

        if ($dow < 6)
        {
            $extramonthcalinsert = mysql_query("INSERT INTO monthcal
                                                VALUES
                                                (
                                                    $extra[0],
                                                    $month,
                                                    $x+1,
                                                    $year,
                                                    'Vac  ',
                                                    1,
                                                    0,
                                                    0
                                                )
                                                ");
            if (!$extramonthcalinsert)
                $cr = FALSE;

            $extramainsert = mysql_query("INSERT INTO monthassignment
                                        VALUES
                                        (
                                            $extra[0],
                                            $month,
                                            $x+1,
                                            $year,
                                            'Vac  ',
                                            1,
                                            '06:30',
                                            2,
                                            '06:00',
                                            96,
                                            0,
                                            now(),
                                            NULL,
                                            NULL
                                        )
                                        ");
            if(!$extramainsert)
                $cr = FALSE;

            $extramninsert = mysql_query("INSERT INTO originalmonthassignments
                                        VALUES
                                        (
                                            $extra[0],
                                            $month,
                                            $x+1,
                                            $year,
                                            'Vac  ',
                                            1,
                                            '06:30',
                                            2,
                                            '06:00',
                                            96,
                                            0,
                                            NULL
                                        )
                                        ");
            if (!$extramninsert)
                $cr = FALSE;
        }
        else
        {
            $extramonthcalinsert = mysql_query("INSERT INTO monthcal
                                                VALUES
                                                (
                                                    $extra[0],
                                                    $month,
                                                    $x+1,
                                                    $year,
                                                    'Vac  ',
                                                    1,
                                                    1,
                                                    0
                                                )
                                                ");
            if (!$extramonthcalinsert)
                $cr = FALSE;

            $extramainsert = mysql_query("INSERT INTO monthassignment
                                        VALUES
                                        (
                                            $extra[0],
                                            $month,
                                            $x+1,
                                            $year,
                                            'Vac  ',
                                            1,
                                            '06:00',
                                            0,
                                            '06:00',
                                            96,
                                            1,
                                            now(),
                                            NULL,
                                            NULL
                                        )
                                        ");
            if(!$extramainsert)
                $cr = FALSE;

            $extramninsert = mysql_query("INSERT INTO originalmonthassignments
                                        VALUES
                                        (
                                            $extra[0],
                                            $month,
                                            $x+1,
                                            $year,
                                            'Vac  ',
                                            1,
                                            '06:00',
                                            0,
                                            '06:00',
                                            96,
                                            1,
                                            NULL
                                        )
                                        ");
            if (!$extramninsert)
                $cr = FALSE;
        }
    }	                                                 	
}
if ($cr)
{
	echo 'All extra data entered.';
}
else
{
        echo 'Error in data entry.';
}
?>
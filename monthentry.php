<?php
/*
 * Version 02_01
 */
/*
 * Revised 2011_08_07
 * Revised 2012-11-19
 * Last revised 2012-12-10
 */
   ///////////////////////////////////////////////////////////////////////////////////////////////
   //WEBSITE CONNECTION
   $con = @mysql_connect('localhost', 'paapaus_dcb', 'srt101');

   if (!$con)
   {
       //LOCAL CONNECTION
       $con = @mysql_connect('localhost', 'root', 'srt101');
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
/*
 * monthnow fields
 * [0] mdnumber
 * [1] monthnumber
 * [2] daynumber
 * [3] yearnumber
 * [4] assignment
 * [5] weekend
 */
/* monthassignment fields
 * [0] mdnumber
 * [1] monthnumber
 * [2] daynumber
 * [3] yearnumber
 * [4] assignment
 * [5] assigntype
 * [6] bt
 * [7] beginblock
 * [8] et
 * [9] endblock
 * [10] weekend
 * [11] entrytime
 * [12] logmd
 * [13] counter
 */
$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));


///OMG////////////////////////////////////////////////////////////////////////////////////////////
//Use this next line if you are running this the month of the actual date instead of the month
//before.
//$datetime->modify("-1 month");
$monthlast = $datetime->format('n');
$year = $datetime->format('Y');
$daysInLastMonth = cal_days_in_month(CAL_GREGORIAN, $monthlast, $year) ;


/*
 * Clean the entries in monthnow in case this has been run already but needs to be run again.
 */

$checkForPCStatement = "SELECT *
                        FROM monthnow
                        WHERE assignment LIKE '%PostCall%'";
$checkForPCQuery = mysql_query($checkForPCStatement);
if (@mysql_num_rows($checkForPCQuery))
{
    while ($a = @mysql_fetch_array($checkForPCQuery))
    {
        $falseassignment = $a['assignment'];
        $realassignment = "";
        for ($x = 0; $x < 5; $x++)
        {
            $realassignment .= $falseassignment[$x];
        }
        
        $s = "UPDATE monthnow
              SET assignment = '$realassignment'
              WHERE mdnumber = {$a['mdnumber']}
              AND monthnumber = {$a['monthnumber']}
              AND daynumber = {$a['daynumber']}
              AND yearnumber = {$a['yearnumber']}
              AND weekend = {$a['weekend']}";
        echo $s."<br>";
        mysql_query($s);
    }
}
        
    



/*
 * Check all the assignments on month day 1 to see if any of these are assignments assigned as
 * /PostCall assignments after a call on the last day of the previous month.
 * This only applies if the first day of the month is not a weekend day.
 */
$checkDayOneStatement = "   SELECT *
                            FROM monthnow
                            WHERE daynumber=1
                            AND weekend=0";
$checkDayOneQuery = mysql_query($checkDayOneStatement);

if (@mysql_num_rows($checkDayOneQuery))
{
    while ($checkDayOneResult = @mysql_fetch_array($checkDayOneQuery))
    {
       $getAssignTypeStatement = "SELECT type_number
                                  FROM assignments
                                  WHERE assignment  LIKE '{$checkDayOneResult['assignment']}%'";
       /*
        * type_number=4 is an OFF day.  If the type_number is not 4 then check to see if the 
        * previous day assignment was a call assignment.
        */
                                  
       $getAssignTypeQuery = mysql_query($getAssignTypeStatement);
       $getAssignTypeResult = @mysql_fetch_array($getAssignTypeQuery);
       if ($getAssignTypeResult['type_number']!=4)
       {
           $checkDayBeforeStatement = "SELECT *
                                       FROM monthpast
                                       WHERE mdnumber={$checkDayOneResult['mdnumber']}
                                       AND daynumber=$daysInLastMonth
                                       AND (assignment LIKE 'C OR%'
                                            ||
                                            assignment LIKE 'C OB%'
                                            ||
                                            assignment LIKE 'C Mat%'
                                            ||
                                            assignment LIKE 'C Hnt%'
                                            ||
                                            assignment LIKE 'C OH%'
                                           )";
           $checkDayBeforeQuery = mysql_query($checkDayBeforeStatement);
           if (@mysql_num_rows($checkDayBeforeQuery))
           {
               $checkDayBeforeResult = @mysql_fetch_array($checkDayBeforeQuery);
               $newassignment = $checkDayOneResult['assignment'];
               $newassignment .= '/PostCall';
               $pcs = "UPDATE monthnow
                       SET assignment='$newassignment'
                       WHERE mdnumber={$checkDayOneResult['mdnumber']}
                       AND daynumber=1";
               echo $pcs;
               echo "<br>";
               mysql_query($pcs);
           }
       }
    }
}
















$checkDayAfterOneStatement = "  SELECT *
                                FROM monthnow
                                WHERE daynumber!=1
                                AND weekend=0
                                ORDER BY mdnumber, daynumber";
$checkDayAfterOneQuery = mysql_query($checkDayAfterOneStatement);

while ($checkDayAfterOneResult = @mysql_fetch_array($checkDayAfterOneQuery))
{
   $dayBefore = $checkDayAfterOneResult['daynumber']-1;
   $getAssignTypeStatement = "SELECT type_number
                              FROM assignments
                              WHERE assignment  LIKE '{$checkDayAfterOneResult['assignment']}%'";
   $getAssignTypeQuery = mysql_query($getAssignTypeStatement);
   $getAssignTypeResult = @mysql_fetch_array($getAssignTypeQuery);
   if ($getAssignTypeResult['type_number']!=4)
   {
       $checkDayBeforeStatement = "SELECT *
                                   FROM monthnow
                                   WHERE mdnumber={$checkDayAfterOneResult['mdnumber']}
                                   AND daynumber=$dayBefore
                                   AND (assignment LIKE 'C OR%'
                                        ||
                                        assignment LIKE 'C OB%'
                                        ||
                                        assignment LIKE 'C Mat%'
                                        ||
                                        assignment LIKE 'C Hnt%'
                                        ||
                                        assignment LIKE 'C OH%'
                                       )";
       $checkDayBeforeQuery = mysql_query($checkDayBeforeStatement);
       if (@mysql_num_rows($checkDayBeforeQuery))
       {
           $checkDayBeforeResult = @mysql_fetch_array($checkDayBeforeQuery);
           $newassignment = $checkDayAfterOneResult['assignment'];
           $newassignment .= '/PostCall';
           $pcs = "UPDATE monthnow
                   SET assignment='$newassignment'
                   WHERE mdnumber={$checkDayAfterOneResult['mdnumber']}
                   AND daynumber={$checkDayAfterOneResult['daynumber']}";
           echo $pcs;
           echo "<br>";
           mysql_query($pcs);
       }
   }
}







$sql = "SELECT *
        FROM monthnow
        ORDER BY mdnumber, daynumber";
$sqlq = mysql_query($sql);


while ($sqlf = @mysql_fetch_array($sqlq))
{
   if (trim($sqlf[4])=='H 1' && $sqlf[5]==1)
   {
     $isql = "INSERT INTO monthassignment
              VALUES (  {$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        'Wkend',
                        1,
                        '06:30',
                        2,
                        '06:00',
                        96,
                        1,
                        now(),
                        NULL,
                        NULL)";
     $isqlq = mysql_query($isql);
     $isql2 = "     INSERT INTO monthassignment
                    VALUES ({$sqlf['mdnumber']},
                            {$sqlf['monthnumber']},
                            {$sqlf['daynumber']},
                            {$sqlf['yearnumber']},
                            'H 1',
                            3,
                            '07:00',
                            4,
                            '07:00',
                            100,
                            1,
                            now(),
                            NULL,
                            NULL)";
     $isqlq2 = mysql_query($isql2);
   }
   else if (trim($sqlf[4])=='H 1' && $sqlf[5]==0)
   {
     $isql = "  INSERT INTO monthassignment
                VALUES ({$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        'H 1',
                        1,
                        '06:30',
                        2,
                        '14:30',
                        34,
                        0,
                        now(),
                        NULL,
                        NULL)";
     $isqlq = mysql_query($isql);
     $isql2 = " INSERT INTO monthassignment
                VALUES ({$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        'H 1',
                        3,
                        '14:30',
                        34,
  			'06:30',
                        98,
                        0,
                        now(),
                        NULL,
                        NULL)";
     $isqlq2 = mysql_query($isql2);
   }
   else if (trim($sqlf[4])=='CWkn2')
   {
      $isql = " INSERT INTO monthassignment
                VALUES ({$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        'Wkend',
                        1,
                        '06:30',
                        2,
                        '06:00',
                        96,
                        1,
                        now(),
                        NULL,
                        NULL)";
      $isqlq = mysql_query($isql);
      $isql2 = "    INSERT INTO monthassignment
                    VALUES ({$sqlf['mdnumber']},
                            {$sqlf['monthnumber']},
                            {$sqlf['daynumber']},
                            {$sqlf['yearnumber']},
                            'CWkn2',
                            3,
                            '07:00',
                            4,
                            '15:00',
                            36,
                            1,
                            now(),
                            NULL,
                            NULL)";
        $isql2 = mysql_query($isql2);
   }
   else if (trim($sqlf[4])=='Pain' && $sqlf[5]==1)
   {
        $isql = "   INSERT INTO monthassignment
                    VALUES ({$sqlf['mdnumber']},
                            {$sqlf['monthnumber']},
                            {$sqlf['daynumber']},
                            {$sqlf['yearnumber']},
                            'Wkend',
                            1,
                            '06:30',
                            2,
                            '06:00',
                            96,
                            1,
                            now(),
                            NULL,
                            NULL)";
        $isqlq = mysql_query($isql);
	 
	 $isql2 = " INSERT INTO monthassignment
                    VALUES ({$sqlf['mdnumber']},
                            {$sqlf['monthnumber']},
                            {$sqlf['daynumber']},
                            {$sqlf['yearnumber']},
                            'PainWE',
                            3,
                            '07:00',
                            4,
                            '07:00',
                            100,
                            1,
                            now(),
                            NULL,
                            NULL)";
		
     $isqlq2 = mysql_query($isql2);
   }
   
   
   /*
    * Dealing with all the calls and whether the day after call has an assignment.
    * A call assignment usually consists of a day portion and a night portion and therefore needs
    * to be split between the two.
    * In any case, the night portion of a call assignment needs to be classified as an 
    * "monthassignment" assigntype 3, not 1
    */
   else if (    (
                trim($sqlf['assignment'])=='C OR'
                ||
                trim($sqlf['assignment'])=='C OB'
                ||
                trim($sqlf['assignment'])=='C Hnt'
                ||
                trim($sqlf['assignment'])=='C Mat'
                ||
                trim($sqlf['assignment'])=='C OH'
                )
            )
   {
        if (trim($sqlf['assignment'])=='C OR' && $sqlf['weekend']==0)
        {
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OR',
                                3,
                                '15:30',
                                38,
                                '06:30',
                                98,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isqm = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'None',
                                1,
                                '06:00',
                                0,
                                '06:00',
                                96,
                                0,
                                now(),
                                NULL,
                                NULL)";
            mysql_query($isqm);
        }
        else if (trim($sqlf['assignment'])=='C OR' && $sqlf['weekend']==1)
        {
            $isql = "INSERT INTO monthassignment
              VALUES (  {$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        'Wkend',
                        1,
                        '06:30',
                        2,
                        '06:00',
                        96,
                        1,
                        now(),
                        NULL,
                        NULL)";
            $isqlq = mysql_query($isql);
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OR',
                                3,
                                '06:30',
                                2,
                                '06:30',
                                98,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
        }
        else if (trim($sqlf['assignment'])=='C OB' && $sqlf['weekend']==0)
        {
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OB',
                                3,
                                '15:30',
                                38,
                                '06:30',
                                98,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isqm = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'None',
                                1,
                                '06:00',
                                0,
                                '06:00',
                                96,
                                0,
                                now(),
                                NULL,
                                NULL)";
            mysql_query($isqm);
        }
        else if (trim($sqlf['assignment'])=='C OB' && $sqlf['weekend']==1)
        {
            $isql = " INSERT INTO monthassignment
                      VALUES (  {$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'Wkend',
                                1,
                                '06:30',
                                2,
                                '06:00',
                                96,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OB',
                                3,
                                '06:30',
                                2,
                                '06:30',
                                98,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
        }
        else if (trim($sqlf['assignment'])=='C Hnt' && $sqlf['weekend']==0)
        {
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Hnt',
                                3,
                                '14:30',
                                34,
                                '06:30',
                                98,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);

            $isqs = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Hnt',
                                1,
                                '06:30',
                                2,
                                '14:30',
                                34,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlr = mysql_query($isqs);
        }
        else if (trim($sqlf['assignment'])=='C Hnt' && $sqlf['weekend']==1)
        {
            $isql = " INSERT INTO monthassignment
                      VALUES (  {$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'Wkend',
                                1,
                                '06:30',
                                2,
                                '06:00',
                                96,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Hnt',
                                3,
                                '06:30',
                                2,
                                '06:30',
                                98,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
        }
        else if (trim($sqlf['assignment'])=='C Mat' && $sqlf['weekend']==0)
        {
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Mat',
                                3,
                                '14:30',
                                34,
                                '06:30',
                                98,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);

            $isqs = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Mat',
                                1,
                                '06:30',
                                2,
                                '14:30',
                                34,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlr = mysql_query($isqs);
        }
        else if (trim($sqlf['assignment'])=='C Mat' && $sqlf['weekend']==1)
        {
            $isql = " INSERT INTO monthassignment
                      VALUES (  {$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'Wkend',
                                1,
                                '06:30',
                                2,
                                '06:00',
                                96,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C Mat',
                                3,
                                '06:30',
                                2,
                                '06:30',
                                98,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
        }
        else if (trim($sqlf['assignment'])=='C OH' && $sqlf['weekend']==0)
        {
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OH',
                                3,
                                '14:00',
                                32,
                                '23:30',
                                70,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);

            $isqs = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OH',
                                1,
                                '06:00',
                                0,
                                '14:00',
                                32,
                                0,
                                now(),
                                NULL,
                                NULL)";
            $isqlr = mysql_query($isqs);
        }
        else if (trim($sqlf['assignment'])=='C OH' && $sqlf['weekend']==1)
        {
            $isql = " INSERT INTO monthassignment
                      VALUES (  {$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'Wkend',
                                1,
                                '06:30',
                                2,
                                '06:00',
                                96,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
            $isql = "   INSERT INTO monthassignment
                        VALUES ({$sqlf['mdnumber']},
                                {$sqlf['monthnumber']},
                                {$sqlf['daynumber']},
                                {$sqlf['yearnumber']},
                                'C OH',
                                3,
                                '06:00',
                                0,
                                '06:00',
                                96,
                                1,
                                now(),
                                NULL,
                                NULL)";
            $isqlq = mysql_query($isql);
        }
   }

   else
   {   
     $jsql = "  SELECT begintime, beginblock, endtime, endblock
                FROM assignments
                WHERE assignment='$sqlf[4]'
                AND weekend = $sqlf[5]
                AND (type_number=1 || type_number=4 || type_number=5 || type_number=7)";
     $jsqlq = mysql_query($jsql);
     
     if (@mysql_num_rows($jsqlq)!=1)
     {
         echo "Error in Making Proper Assignment!/~Line728";
         echo "<br>";
         echo "Assignment - ".$sqlf[4].",  MD - ".$sqlf['mdnumber'].", Day - ".$sqlf['daynumber'];
         echo "<br>";
     }
     
     $jsqlf = mysql_fetch_array($jsqlq);
     
     $isql = "  INSERT INTO monthassignment
                VALUES ({$sqlf['mdnumber']},
                        {$sqlf['monthnumber']},
                        {$sqlf['daynumber']},
                        {$sqlf['yearnumber']},
                        '{$sqlf['assignment']}',
                        1,
                        '{$jsqlf['begintime']}',
                        {$jsqlf['beginblock']},
                        '{$jsqlf['endtime']}',
                        {$jsqlf['endblock']},
                        $sqlf[5],
                        now(),
                        NULL,
                        NULL)";
     $isqlq = mysql_query($isql);
   }
}


$datetime = new DateTime("now", new DateTimeZone('US/Eastern'));



//OMG/////////////////////////////////////////////////////////////////////////////////////////////
//Use this next line if you are using this program the month before the actual dates.
$datetime->modify("+30 days");



$monthnext = $datetime->format('n');
$year = $datetime->format('Y');

$originalMonthAssignmentStatement = "   SELECT *
                                        FROM monthassignment
                                        WHERE monthnumber=$monthnext
                                        AND yearnumber=$year";
$originalMonthAssignmentQuery = mysql_query($originalMonthAssignmentStatement);
while ($originalMonthAssignmentResult = mysql_fetch_array($originalMonthAssignmentQuery))
{
    $originalMonthAssignmentInsertStatement = " INSERT INTO originalmonthassignments
                                                VALUES
                                                (
                                                {$originalMonthAssignmentResult['mdnumber']},
                                                {$originalMonthAssignmentResult['monthnumber']},
                                                {$originalMonthAssignmentResult['daynumber']},
                                                {$originalMonthAssignmentResult['yearnumber']},
                                                '{$originalMonthAssignmentResult['assignment']}',
                                                {$originalMonthAssignmentResult['assigntype']},
                                                '{$originalMonthAssignmentResult['bt']}',
                                                {$originalMonthAssignmentResult['beginblock']},
                                                '{$originalMonthAssignmentResult['et']}',
                                                {$originalMonthAssignmentResult['endblock']},
                                                {$originalMonthAssignmentResult['weekend']},
                                                {$originalMonthAssignmentResult['counter']}
                                                )";
    mysql_query($originalMonthAssignmentInsertStatement);
}

echo 'All entries into tables MONTHASSIGNMENT and PRIMARYASSIGN have been made';
?>

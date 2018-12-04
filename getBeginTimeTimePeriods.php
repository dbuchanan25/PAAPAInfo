
<?php
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-07
 */
/*
 * Called from home.php
 */
function getBeginTimeTimePeriods($n)
{
    $beginblockquery = mysql_query("SELECT beginblock, endblock, type_number
                                    FROM assignments
                                    WHERE n=$n");
    $beginblocks     = @mysql_fetch_row($beginblockquery);

    
    if (!$beginblocks || $beginblocks[2]==4)
    {
       $beginTimePeriodQuery = ("SELECT time, timeperiod
                                 FROM timeperiods
                                 WHERE timeperiod>=0
                                 AND timeperiod<100");
        $beginBlockResult = mysql_query($beginTimePeriodQuery);
        return $beginBlockResult;
    }
    else
    {
        $beginTimePeriodQuery = ("SELECT time, timeperiod
                                  FROM timeperiods
                                  WHERE timeperiod>=$beginblocks[0]
                                  AND timeperiod<$beginblocks[1]");
        $beginBlockResult = mysql_query($beginTimePeriodQuery);
        return $beginBlockResult;
   }
}
?>

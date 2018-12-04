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
function getEndTimeTimePeriods($n)
{
    $endblockquery      = mysql_query("SELECT beginblock, endblock, type_number
                                       FROM assignments
                                       WHERE n=$n");
    $endblocks          = @mysql_fetch_row($endblockquery);
    


    if (!$endblocks || $endblocks[2]==4)
    {
        $endTimePeriodQuery = ("SELECT time, timeperiod
                                FROM timeperiods
                                WHERE timeperiod>0
                                AND timeperiod<=100");
        $endBlockResult = mysql_query($endTimePeriodQuery);
        return $endBlockResult;
    }
    else
    {
        $endTimePeriodQuery = ("SELECT time, timeperiod
                                FROM timeperiods
                                WHERE timeperiod>$endblocks[0]
                                AND timeperiod<=$endblocks[1]");
        $endBlockResult = mysql_query($endTimePeriodQuery);
        return $endBlockResult;
    }
}
?>

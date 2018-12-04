<?php

function checkForHoliday($day, $month, $year)
{
    $christmas = new DateTime();
    $thanksgiving = new DateTime(); //fourth Thursday of November
    $laborday = new DateTime(); //first Monday of September
    $independenceday = new DateTime();
    $memorialday = new DateTime(); //last Monday in May
    $easter = new DateTime(); //varies, done by table through 2025
    
    $strDate = $year."-".$month."-".$day;
    
    $today = date_create($strDate);
    $thisyear = $today->format('Y');
    $todayarray = getdate(date_timestamp_get($today));
    $todaydoy = $todayarray['yday'];
    //echo '<br>Today: '.$todaydoy.'<br>';
    
    
    $christmas->setDate($thisyear, 12, 25);
    $christmasarray = getdate(date_timestamp_get($christmas));
    $christmasdoy = $christmasarray['yday'];
    //echo '<br>Christmas: '.$christmasdoy.'<br>';
    
    switch ($thisyear)
    {
        case '2014':
        {
            $easter->setDate(2014,4,20);
            break;
        }
        case '2015':
        {
            $easter->setDate(2015,4,5);
            break;
        }
        case '2016':
        {
            $easter->setDate(2016,4,27);
            break;
        }
        case '2017':
        {
            $easter->setDate(2017,4,16);
            break;
        }
        case '2018':
        {
            $easter->setDate(2018,4,1);
            break;
        }
        case '2019':
        {
            $easter->setDate(2019,4,21);
            break;
        }
        case '2020':
        {
            $easter->setDate(2020,4,12);
            break;
        }
        case '2021':
        {
            $easter->setDate(2021,4,4);
            break;
        }
        case '2022':
        {
            $easter->setDate(2022,4,17);
            break;
        }
        case '2023':
        {
            $easter->setDate(2023,4,9);
            break;
        }
        case '2024':
        {
            $easter->setDate(2024,3,31);
            break;
        }
        case '2025':
        {
            $easter->setDate(2025,4,20);
            break;
        }
    }
    $easterarray = getdate(date_timestamp_get($easter));
    $easterdoy = $easterarray['yday'];
    //echo '<br>Easter: '.$easterdoy.'<br>';
    

    if ($thisyear == 2015) {
       $independenceday->setDate($thisyear,7,3); 
    }
    else {
        $independenceday->setDate($thisyear,7,4);
    }
    $independencedayarray = getdate(date_timestamp_get($independenceday));
    $independencedaydoy = $independencedayarray['yday'];
    //echo '<br>Independence Day: '.$independencedaydoy.'<br>';
    
    
    
    $firstSeptember = new DateTime();
    $firstSeptember->setDate($thisyear,9,1);
    $firstDaySeptemberDOW = $firstSeptember->format('w'); //Sunday=0, Saturday=6
    if ($firstDaySeptemberDOW === 1) { //Monday=1
        $laborday = $firstSeptember;
    }
    else if ($firstDaySeptemberDOW === 0)
    {
        $laborday->setDate($thisyear,9,2);
    }
    else
    {
        $laborday->setDate($thisyear, 9, 9-$firstDaySeptemberDOW);
    }
    $labordayarray = getdate(date_timestamp_get ($laborday));
    $labordaydoy = $labordayarray['yday'];
    //echo '<br>Labor Day: '.$labordaydoy.'<br>';
    
    
    
    
    $firstNovember = new DateTime();
    $firstNovember->setDate($thisyear,11,1);
    $firstDayNovemberDOW = $firstNovember->format('w'); //Sunday=0, Saturday=6
 
    if ($firstDayNovemberDOW <= 4)
    {
        $thanksgiving->setDate($thisyear, 11, 28 + (5 - $firstDayNovemberDOW)); //Thursday=4
    }
    else 
    {
        $thanksgiving->setDate($thisyear, 11, 21 + ($firstDayNovemberDOW));
    }
    $thanksgivingdayarray = getdate(date_timestamp_get ($thanksgiving));
    $thanksgivingdaydoy = $thanksgivingdayarray['yday'];
    //echo '<br>Thanksgiving Day: '.$thanksgivingdaydoy.'<br>';
    
    
    
    
    $lastMay = new DateTime();
    $lastMay->setDate($thisyear,5,31);
    $lastMayDOW = $lastMay->format('w'); //Sunday=0, Saturday=6
 
    if ($lastMayDOW >= 1) {
        $memorialday->setDate ($thisyear, 5, 31 - $lastMayDOW + 1);
    }
    else 
    {
        $memorialday->setDate($thisyear, 5, 25);
    }
    $memorialdayarray = getdate(date_timestamp_get ($memorialday));
    $memorialdaydoy = $memorialdayarray['yday'];
    //echo '<br>Memorial Day: '.$memorialdaydoy.'<br><br>';
    
    /*
    if (   ((($easterdoy-5) <= $todaydoy) && ($todaydoy <= $easterdoy))
        || ((($memorialdaydoy-5) <= $todaydoy) && ($todaydoy <= $memorialdaydoy))
        || ((($independencedaydoy-5) <= $todaydoy) && ($todaydoy <= $independencedaydoy))
        || ((($labordaydoy-5) <= $todaydoy) && ($todaydoy <= $labordaydoy))
        || ((($thanksgivingdaydoy-5) <= $todaydoy) && ($todaydoy <= $thanksgivingdaydoy))
        || ($todaydoy >= ($christmasdoy-5))
       ) {
        return true;
       }
    else {
        return false;
    }
     * 
     */
    if ($todaydoy == 1 || $todaydoy == $easterdoy || $todaydoy == $memorialdaydoy
        || $todaydoy == $independencedaydoy || $todaydoy == $labordaydoy 
        || $todaydoy == $thanksgivingdaydoy || $todaydoy == $christmasdoy) {
        return true;
    }
    else {
        return false;
    }       
}
?>

<?php
//session_start();
$page_title = "PAAPA Orthopedic";
include ('includes/header.php');

/*
 * Version 01_01
 */	 

echo '<center><h1>Orthopedic Call Assignments</h1></center><br><br>';

echo '
<style type="text/css">
.myTable 
{ 
	background-color: D7DAE1;
        width: 60%;
        margin-left: 20%;
        margin-right: 20%;
}
 
.myTable td, .myTable th 
{ 
        border-width: 1px; 
        border-style: solid;
        border-color: black;
        background-color: D7DAE1;
        text-align: center;
}
</style>';



$web = false;

if ($web)
{
    DEFINE ('DB_USER', 'paapaus_db25');
    DEFINE ('DB_PASSWORD', '!Srt101!');
    DEFINE ('DB_HOST', 'localhost');
    $dbc2 = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD);
    if ($dbc2)
    {
        mysql_select_db('paapaus_anesthesiapay', $dbc2);
    }
    else 
    {
      echo 'Failure';  
    }
}
else 
{
    DEFINE ('DB_USER', 'root');
    DEFINE ('DB_PASSWORD', '');
    DEFINE ('DB_HOST', 'localhost');
    
    $dbc2 = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD);
    
    if ($dbc2)
    {
        mysql_select_db('anesthesiapay', $dbc2);
    }
    else 
    {
      echo 'Failure';  
    }      
}



$datetimeToday = new DateTime("now", new DateTimeZone('US/Eastern'));
$date = $datetimeToday->format('d');
$year = $datetimeToday->format('Y');
$month = $datetimeToday->format('n');
$monthText = $datetimeToday->format('F');
$dowText = $datetimeToday->format('l');


$orthoCallStatement = 
"
SELECT * 
FROM `monthassignment` 
WHERE `monthnumber` = $month
AND `daynumber` = $date
AND `yearnumber` = $year
AND `assignment` LIKE 'C OH%'
ORDER BY beginblock
";

$orthoQuery = mysql_query($orthoCallStatement);


echo '<center><h2>'.$dowText.', '.$monthText.' '.$date.', '.$year.'</h2></center><br>';
echo '  <table class="myTable">
            <tr>               
                <th width=10%>
                    Begin Time
                </th>
                <th width=10%>
                    End Time
                </th>
                <th width=20%>
                    Anesthesiologist
                </th>
                <th width=20%>
                    Anesthesiologist Number
                </th>                
            </tr>';

while (@$orthoAnswer = mysql_fetch_array($orthoQuery))
{
    $getMDStatement = 
    "
    SELECT last
    FROM mds
    WHERE number = {$orthoAnswer['mdnumber']}
    ";
    $MDQuery = mysql_query($getMDStatement);
    @$MDName = mysql_fetch_array($MDQuery);
    $last = $MDName['last'];
echo '      <tr>                
                <td width=10%>
                '.$orthoAnswer['bt'].'               
                </td>
                <td width=10%>
                '.$orthoAnswer['et'].'
                </td>
                <td width=20%>
                   Dr. '.$last.'</td>
                </td>
                <td width=10%>'
                    .$orthoAnswer['mdnumber'].'
                </td>                
            </tr>
     ';
}

            
echo '  </table><br><br>';


date_add($datetimeToday, date_interval_create_from_date_string('1 days'));

$date = $datetimeToday->format('d');
$year = $datetimeToday->format('Y');
$month = $datetimeToday->format('n');
$monthText = $datetimeToday->format('F');
$dowText = $datetimeToday->format('l');



$orthoCallStatement = 
"
SELECT * 
FROM `monthassignment` 
WHERE `monthnumber` = $month
AND `daynumber` = $date
AND `yearnumber` = $year
AND `assignment` LIKE 'C OH%'
ORDER BY beginblock
";

$orthoQuery = mysql_query($orthoCallStatement);


echo '<center><h2>'.$dowText.', '.$monthText.' '.$date.', '.$year.'</h2></center><br>';
echo '  <table class="myTable">
            <tr>               
                <th width=10%>
                    Begin Time
                </th>
                <th width=10%>
                    End Time
                </th>
                <th width=20%>
                    Anesthesiologist
                </th>
                <th width=20%>
                    Anesthesiologist Number
                </th>                
            </tr>';

while (@$orthoAnswer = mysql_fetch_array($orthoQuery))
{
    $getMDStatement = 
    "
    SELECT last
    FROM mds
    WHERE number = {$orthoAnswer['mdnumber']}
    ";
    $MDQuery = mysql_query($getMDStatement);
    @$MDName = mysql_fetch_array($MDQuery);
    $last = $MDName['last'];
echo '      <tr>                
                <td width=10%>
                '.$orthoAnswer['bt'].'               
                </td>
                <td width=10%>
                '.$orthoAnswer['et'].'
                </td>
                <td width=20%>
                   Dr. '.$last.'</td>
                </td>
                <td width=10%>'
                    .$orthoAnswer['mdnumber'].'
                </td>                
            </tr>
     ';
}

            
echo '  </table><br><br>';


date_add($datetimeToday, date_interval_create_from_date_string('1 days'));

$date = $datetimeToday->format('d');
$year = $datetimeToday->format('Y');
$month = $datetimeToday->format('n');
$monthText = $datetimeToday->format('F');
$dowText = $datetimeToday->format('l');



$orthoCallStatement = 
"
SELECT * 
FROM `monthassignment` 
WHERE `monthnumber` = $month
AND `daynumber` = $date
AND `yearnumber` = $year
AND `assignment` LIKE 'C OH%'
ORDER BY beginblock
";

$orthoQuery = mysql_query($orthoCallStatement);


echo '<center><h2>'.$dowText.', '.$monthText.' '.$date.', '.$year.'</h2></center><br>';
echo '  <table class="myTable">
            <tr>               
                <th width=10%>
                    Begin Time
                </th>
                <th width=10%>
                    End Time
                </th>
                <th width=20%>
                    Anesthesiologist
                </th>
                <th width=20%>
                    Anesthesiologist Number
                </th>                
            </tr>';

while (@$orthoAnswer = mysql_fetch_array($orthoQuery))
{
    $getMDStatement = 
    "
    SELECT last
    FROM mds
    WHERE number = {$orthoAnswer['mdnumber']}
    ";
    $MDQuery = mysql_query($getMDStatement);
    @$MDName = mysql_fetch_array($MDQuery);
    $last = $MDName['last'];
echo '      <tr>                
                <td width=10%>
                '.$orthoAnswer['bt'].'               
                </td>
                <td width=10%>
                '.$orthoAnswer['et'].'
                </td>
                <td width=20%>
                   Dr. '.$last.'</td>
                </td>
                <td width=10%>'
                    .$orthoAnswer['mdnumber'].'
                </td>                
            </tr>
     ';
}

            
echo '  </table><br><br>';
?>
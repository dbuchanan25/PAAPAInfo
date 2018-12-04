<?php
/*
 * Version 02_01
 */
/*
 * Last Revised 2011-08-27
 */
/*
 * Called from day_display_code1.php
 */
/*
 * Revised 2011-08-27 to consolidate the two tables into one table which holds the hours
 * information with the colored blocks showing the type of assignment a partner has.  "colspan=4"
 * used on the hours bar to accomplish this.
 */
function day_display_code2()
{
echo'
<table align="center" width="100%" 
    style="border-width:1px; border-style:solid; border-color:#808080;">
    <tr>';
$hour=6;
for ($x=0; $x<25; $x++)
{
    if ($hour>23)
        $hour=0;
    if ($hour<10)
    echo'
        <td  colspan="4" height="25" align="center" width=4% 
            style="border-width:1px; border-style:solid;border-color:#808080; font-size:12">0'
        .$hour.'00</td>';
    else
    echo'
        <td  colspan="4" height="25" align="center" width=4% 
            style="border-width:1px; border-style:solid;border-color:#808080; font-size:12">'
        .$hour.'00</td>';
    $hour++;
}
echo'
    </tr>

    ';
}
?>

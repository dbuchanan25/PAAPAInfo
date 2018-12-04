<?php

function xmlparsem($st,$comp)
{
    $y = strlen($st);
    $yy = strlen($comp);
    $rs = "";

    $comp_end = "</";

    $comp_end .= substr($comp,1);
    

    for ($x=0; $x<($y-$yy); $x++)
    {   
        if (substr($st,$x,$yy)===$comp)
        {
            $x+=$yy;
            while (substr($st,$x,$yy+1)!= $comp_end)
            {
                $rs .= $st[$x];
                $x++;
            }
            $rs.= ",";
        }
    }
    $z = strlen($rs);
    $rt = substr($rs,0,$z-1);
    $myArray = explode(',', $rt);    
    return $myArray;
}

function xmlparse($st,$comp)
{
    $y = strlen($st);
    $yy = strlen($comp);
    $rs = "";

    $comp_end = "</";

    $comp_end .= substr($comp,1);
    

    for ($x=0; $x<($y-$yy); $x++)
    {   
        if (substr($st,$x,$yy)===$comp)
        {
            $x+=$yy;
            while (substr($st,$x,$yy+1)!= $comp_end)
            {
                $rs = $rs.$st[$x];
                $x++;
            }
        }
    }   
    return $rs;
}
?>


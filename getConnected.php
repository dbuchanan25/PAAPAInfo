<?php

function getConnected() {
    $con = @mysql_connect('localhost', 'paapaus_dcb', 'srt101');

    if ($con) {
       $db_selected = @mysql_select_db("paapaus_anesthesiapay", $con);
    }
    
    if (!isset($db_selected)) {
        //LOCAL CONNECTION
        $conlocal = @mysql_connect('localhost', 'root', '');
        if ($conlocal)
            $db_selected_local = @mysql_select_db("anesthesiapay", $conlocal);
        else if (!isset($db_selected) && !isset($db_selected_local))
        {
            die('Could not connect: ' . mysql_error());
        }
    }
}

function closeConnected() {
    if ($con)
        mysql_close($con);
    else
        mysql_close($conlocal);
}


<?php # Script 8.2 - mysqli_connect.php

//This file contains the database access information.
//This file also establishes a connection to MySQL and selects the database.

//Set the database access information as constants:


try{
    DEFINE ('DB_USER', 'paapaus_dcb');
    DEFINE ('DB_PASSWORD', 'srt101');
    DEFINE ('DB_HOST', 'localhost');


    $dbc2 = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD);
    
    if ($dbc2){
        mysql_select_db('paapaus_anesthesiapay', $dbc2);
    }
    
    else {
        DEFINE ('DB_USER2', 'root');
        DEFINE ('DB_PASSWORD2', '');
        DEFINE ('DB_HOST2', 'localhost');


        $dbc2 = @mysql_connect (DB_HOST2, DB_USER2, DB_PASSWORD2);
        if ($dbc2){
            mysql_select_db('anesthesiapay', $dbc2);
        }
        else {
            throw new Exception("Could not connect to database");
        }
    }
}
catch (Exception $e){
    echo $e;
}

?>
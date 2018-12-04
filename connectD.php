<?php # Script 8.2 - mysqli_connect.php

//This file contains the database access information.
//This file also establishes a connection to MySQL and selects the database.

//Set the database access information as constants:


try {
    DEFINE ('DB_USER', 'paapaus_amanda');
    DEFINE ('DB_PASSWORD', 'Aml112358');
    DEFINE ('DB_HOST', 'localhost');
    DEFINE ('DB_NAME', 'paapaus_patientsatisfaction');


    $dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if (!$dbc) {
        if (!$dbc){
            throw new Exception("Could not connect to database");
        }
    }
 else {
      echo "Connection successful<br>";  
    }
}
catch (Exception $e){
    echo $e;
}

?>

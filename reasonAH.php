<?php
if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['initials'])){
    require_once ('includes/login_functions.inc.php');
    $url = absolute_url();
    header("Location: $url");
    exit();
}

else if (!isset($_SESSION['reason']))
{
    /*
    echo 'HERE1';
    echo '<br>SESSION variable reason = ';
    var_dump($_SESSION['reason']);
    echo '<br>SESSION variable ahr = ';
    var_dump($_SESSION['ahr']);
     * 
     */
    require_once ($_SESSION['login2string']);
    echo'
    <link rel="stylesheet" href="style.css" type="text/css">
    ';


    /*
     * make $_SESSION variables out of the added hours information
     */
    if (isset($_SESSION['reasonaddedhours1']) || isset($_REQUEST["reasonaddedhours1"])) {
    $_SESSION["reasonaddedhours1"] = $_REQUEST["reasonaddedhours1"];
    $_SESSION ["btaddedhours1"] = $_REQUEST ["btaddedhours1"];
    $_SESSION ["etaddedhours1"] = $_REQUEST ["etaddedhours1"];
    $ahlevel = 1;
    }

    if (isset($_SESSION['reasonaddedhours2']) || isset($_REQUEST["reasonaddedhours2"])) {
    $_SESSION["reasonaddedhours2"] = $_REQUEST["reasonaddedhours2"];
    $_SESSION ["btaddedhours2"] = $_REQUEST ["btaddedhours2"];
    $_SESSION ["etaddedhours2"] = $_REQUEST ["etaddedhours2"];
    $ahlevel = 2;
    }

    if (isset($_SESSION['reasonaddedhours3']) || isset($_REQUEST["reasonaddedhours3"])) {
    $_SESSION["reasonaddedhours3"] = $_REQUEST["reasonaddedhours3"];
    $_SESSION ["btaddedhours3"] = $_REQUEST ["btaddedhours3"];
    $_SESSION ["etaddedhours3"] = $_REQUEST ["etaddedhours3"];
    $ahlevel = 3;
    }



if ((!isset($_SESSION['ahr'])) &&
   (
   (isset($_SESSION['reasonaddedhours1']) && $ahlevel == 1 &&
         ($_SESSION['reasonaddedhours1'] == 60 || 
          $_SESSION['reasonaddedhours1'] == 61 || 
          $_SESSION['reasonaddedhours1'] == 64 ||
          $_SESSION['reasonaddedhours1'] == 65 || 
          $_SESSION['reasonaddedhours1'] == 153 || 
          $_SESSION['reasonaddedhours1'] == 154))
    ||
    isset($_SESSION['reasonaddedhours2']) && $ahlevel == 2 &&
         ($_SESSION['reasonaddedhours2'] == 60 || 
          $_SESSION['reasonaddedhours2'] == 61 || 
          $_SESSION['reasonaddedhours2'] == 64 ||
          $_SESSION['reasonaddedhours2'] == 65 || 
          $_SESSION['reasonaddedhours2'] == 153 || 
          $_SESSION['reasonaddedhours2'] == 154)
    ||
    isset($_SESSION['reasonaddedhours3']) && $ahlevel == 3 &&
         ($_SESSION['reasonaddedhours3'] == 60 || 
          $_SESSION['reasonaddedhours3'] == 61 || 
          $_SESSION['reasonaddedhours3'] == 64 ||
          $_SESSION['reasonaddedhours3'] == 65 || 
          $_SESSION['reasonaddedhours3'] == 153 || 
          $_SESSION['reasonaddedhours3'] == 154)
    )
    ) {
        $page_title = 'Added Hours Reason';
        echo '<title>'.$page_title.'</title>';
        include_once ('includes/header.php');
        $_SESSION['ahr'] = true;
        $_SESSION['reason'] = false;



                echo'
                <form method="post" action="reasonAH.php">
                <table width="100%" style="border:1px solid black;">
                    <tr>
                        <td width="20%" align="center" bgcolor="#D7DAE1">
                        Added Hours Explanation:
                        </td>
                        <td width="80%" align="left" style="border:1px solid black;">
                            <input type="text" size="300" name="commnt"
                                value="Enter Added Hours Reason & Comments Here.">
                        </td>
                    </tr>
                </table>
                <br><br>


                        <input type="submit" name="submit" value="Submit" class="buttn">

                        
                </form>';
    }
 else {
      header ('Location: day_display.php');  
    }
    /*
    else {
        $reasonText = "INSERT INTO ahr VALUES (NULL,".
                         $_SESSION['mdn'].", ".
                         $_SESSION['dtm'].", ".
                         $_SESSION['dai'].", ".
                         $_SESSION['dty'].", '".
                         $_SESSION['initials']."', '".
                         $_REQUEST['commnt']."')";
        mysql_query($reasonText);
        unset($_SESSION['reason']);
        //unset($_SESSION['reasonaddedhours1']);
        //unset($_SESSION['reasonaddedhours2']);
        //unset($_SESSION['reasonaddedhours3']);
        header ('Location: day_display.php');
    }
     * 
     */
}
    
else if ($_SESSION['reason'] == false) { 
    require_once ($_SESSION['login2string']);
    if (strncmp($_REQUEST['commnt'], 'Enter',5) == 0)
    {
        ?>
            <script type="text/javascript">
            alert("You did not put a reason for adding hours.\n"+
                  "Please try again.\n");
            </script>
        <?php
        $page_title = 'Added Hours Reason';
        echo '<title>'.$page_title.'</title>';
        include_once ('includes/header.php');
        $_SESSION['ahr'] = true;

        echo'
        <form method="post" action="reasonAH.php">
        <table width="100%" style="border:1px solid black;">
            <tr>
                <td width="20%" align="center" bgcolor="#D7DAE1">
                Added Hours Explanation:
                </td>
                <td width="80%" align="left" style="border:1px solid black;">
                    <input type="text" size="300" name="commnt"
                        value="Enter Added Hours Reason & Comments Here.">
                </td>
            </tr>
        </table>
        <br><br>


                <input type="submit" name="submit" value="Submit" class="buttn">                        
        </form>';
    }
    else {
        $reasonText = "INSERT INTO ahr VALUES (NULL,".
                         $_SESSION['mdn'].", ".
                         $_SESSION['dtm'].", ".
                         $_SESSION['dai'].", ".
                         $_SESSION['dty'].", '".
                         $_SESSION['initials']."', '".
                         $_REQUEST['commnt']."')";
        mysql_query($reasonText);
        unset($_SESSION['reason']);
        header ('Location: day_display.php');
    }
}
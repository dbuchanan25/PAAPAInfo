<?php
if (!isset($_SESSION)) { session_start(); }
require_once ($_SESSION['login2string']);
echo'
    <link rel="stylesheet" href="style.css" type="text/css">
';


/*
 * Version 02_05
 */
/*
 * Last Revised: 2016-09-13
 * Revised: 2015-08-01
 * Revised: 2015-07-04,05
 * Revised: 2015-01-01
 * Revised: 2014-01
 * Revised: 2011-08-22
 */
/*
 * Revised 2016-09-13 to institute new pay rules giving weekday call extra 
 * value.
 */
/*
 * Revised 2015-08-01 to institute new rules on business hours
 * 1.  If a partner gets regular business days, then they don't get to add
 * business hours.
 * 2.  If a partner is assigned any business days during the month, 
 * they don't get to add any more business hours.
 * 3.  The limit for the number of business hours added is 8.0 hrs/month.
 */
/*
 * Revised 2015-07-04 to incorporate business hours
 */
/*
 * Revision 2015-01 to have the month and year automatically selected and to 
 * add a header and stylesheet.
 */
/*
 * Revision 2014-01 to add and round correctly.
 */
/*
 * Revision 2011-08-22 done to update for the new pay rules
 */



if (!isset($_SESSION['initials']))
{
   require_once ('includes/login_functions.inc.php');
   $url = absolute_url();
   header("Location: $url");
   exit();
}
else
{
    include ('includes/header.php');

    echo '<title>Pay Percentages</title>';
    
    
    ////////////////////////////////////////////////////////////////////////////
    /*
     * Set the maximum number of business quarter hours
     * Now 8 hours or 32 time periods (2015-07-04)
     */
    ////////////////////////////////////////////////////////////////////////////
    $max_bus_time_periods = 32;
    $businessDayAssigned = false;
    ////////////////////////////////////////////////////////////////////////////

    
    
    
    $mdentryStatement = "INSERT INTO mdlog ".
    "VALUES ('{$_SESSION['initials']}','', 'payreport.php accessed', CURRENT_TIMESTAMP, NULL)";
    mysql_query($mdentryStatement);

    $datetime = new DateTime("now", new DateTimeZone('US/Eastern'));
    $monthlast = $datetime->format('n');
    $year = $datetime->format('Y');


    bcscale(10);
    $secondTotalPay = 0.0;
    
    
    /*
     * If the page is being accessed for the first time and the month &
     * year have not already been chosen.  The post method recalls the page
     * with the selected month and year.
     */
    if (empty ($_REQUEST['mnt'])) {
        
        include_once 'menuBar.php';
        menuBar(1567);
        
        echo'
            <br><br>
            <form method="post" action="payreport.php" class="input">
            <table class=table5>
                <tr>
                    <td width="300">
                    </td>
                    <td width="300" align="center"><h2>Create Pay Report For Month:</h2>
                    </td>
                    <td width="300">
                    </td>
                </tr>
                <tr>
                    <td width="300" align="center">
                    <select name="mnt">
             ';

        for ($x=1; $x<=12; $x++) {
            if ($x == $monthlast)
                echo "<option selected='selected'>".$x."</option>\n";
            else
                echo "<option>".$x."</option>\n";
        }

        echo'
                    </select>
                    </td>
                    <td width="300">
                    </td>
                    <td width="300" align="center">
                    <select name="yar">
            ';

        for ($x=2016; $x<=2018; $x++) {
            if ($x == $year)
                echo "<option selected='selected'>".$x."</option>\n";
            else
                echo "<option>".$x."</option>\n";
        }

        echo'
                    </select>
                    </td>
                </tr>
            </table>
            <br>
            ';

        echo'
            <table class=table5>
                <tr>
                    <td align="center">
                    <input type="submit" name="submit" value="Submit" 
                        class="btn">
                    </td>
                </tr>
            </table>
            ';
    }

    
    
    
    
    
    
    else {
   /*
    * Cycle through each of the partners
    */
        $sqlq = "SELECT DISTINCT number, last, initials, payfraction, admin, business ".
                "FROM mds ".
                "WHERE number < 900 ".
                "ORDER BY number";
        $sqlqu = mysql_query($sqlq);
        
       /*
        * Initialize $x, $businessQH, $businessDQH
        */
        
        $mdpay = array();

        $x = 0;
        $counterA = 0;
        
        $businessDQH = array();
        $businessQH = array();
        
        $businessQH[0] = 0.0;
        $businessDQH[0] = 0.0;
        $mdpay[0] = 0.0;
        $totalpay = 0.0;
        
        /*
        * Cycle through each partner and calculate pay variables
        */
        while ($sqlmd = mysql_fetch_array($sqlqu)) {
            
            $sqlq1 = "SELECT assignment, weekend, beginblock, endblock, assigntype ".
                     "FROM monthassignment ".
                     "WHERE monthnumber={$_REQUEST['mnt']} ".
                     "AND yearnumber={$_REQUEST['yar']} ".
                     "AND mdnumber=$sqlmd[0]";
            $sqlq2 = mysql_query($sqlq1);
            
            
            /*
             * Check to see if this partner has been assigned a 
             * 'S Bus' day during the month
             */
            
            $checkBD = "SELECT count(*) ".
                       "FROM monthassignment ".
                       "WHERE monthnumber={$_REQUEST['mnt']} ".
                       "AND yearnumber={$_REQUEST['yar']} ".
                       "AND mdnumber=$sqlmd[0] ".
                       "AND assignment LIKE 'S Bus%'";
            $checkBDQuery = mysql_query($checkBD);
            $checkBDResult = mysql_fetch_array($checkBDQuery);
            if ($checkBDResult[0] > 0) {
                $businessDayAssigned = true;
            }
            else {
                $businessDayAssigned = false;
            }
            
            
            /*
             * Cycle through each assignment for this partner for this month
             */

            while ($sqlq3 = mysql_fetch_array($sqlq2)) {
                
                    
                if ($sqlq3['assigntype']==1) {
                    /*
                     * keep track of the business days seperately
                     */
                    if (trim($sqlq3['assignment']) == 'S Bus') {
                        $businessDQH[$counterA] += ($sqlq3['endblock']-$sqlq3['beginblock']);
                    }
                    else if ((trim($sqlq3['assignment']) == 'Unwanted Vac') || (trim($sqlq3['assignment']) == 'Vac')
                            || (trim($sqlq3['assignment']) == 'UwVac') || (trim($sqlq3['assignment']) == 'None')
                            || (trim($sqlq3['assignment']) == 'Off') || (trim($sqlq3['assignment']) == 'Wkend')
                            || (trim($sqlq3['assignment']) == 'Hlday') || (trim($sqlq3['assignment']) == 'OhOff')
                            || (trim($sqlq3['assignment']) == 'ObOff') || (trim($sqlq3['assignment']) == 'OrOff')
                            || (trim($sqlq3['assignment']) == 'M Off') || (trim($sqlq3['assignment']) == 'H Off')
                            ) {
                    }
                    else {                                                    
                        $sq1 = "SELECT payincrement FROM assignments ".
                               "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                               "AND weekend={$sqlq3['weekend']} ".
                               "AND ( ".
                                       "type_number = 1 ".
                                       "OR ".
                                       "type_number = 4 ".
                                       "OR ".
                                       "type_number = 5 ".
                                   ")";
                        //echo $sq1.'<br>';   
                        $sq2 = mysql_query($sq1);
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            //echo $sq1.'<br>';
                            $sq2 = mysql_query($sq1);
                        }
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 1 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 5 ".
                                       ")";
                            //echo $sq1.'<br>';
                            $sq2 = mysql_query($sq1);
                        }
                        
                        if ($sq2 && (mysql_num_rows($sq2)==1)) {
                           $sq3 = mysql_fetch_array($sq2);
                        }
                        else { 
                            echo $sq1.'<br>';
                        ?>
                            <script type="text/javascript">
                            alert("There is an error in calculating assigntype 1 pay amounts.\n"+
                                  "Report Error Assigntype 1\n"+
                                  "to Dale Buchanan for correction.");
                            </script>
                        <?php                             
                        }
                        $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                    }
                }
                
                
                
                
                else if ($sqlq3['assigntype']==3) {
                    $sq1 = "SELECT payincrement FROM assignments ".
                           "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                           "AND weekend={$sqlq3['weekend']} ".
                           "AND ( ".
                                   "type_number = 2 ".
                                   "OR ".
                                   "type_number = 4 ".
                                   "OR ".
                                   "type_number = 6 ".
                               ")";
                    $sq2 = mysql_query($sq1);
                    
                    if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                     if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                    
                    
                    if ($sq2 && (mysql_num_rows($sq2)==1)) {
                        $sq3 = mysql_fetch_array($sq2);
                    }
                    else {
                    ?>
                        <script type="text/javascript">
                        alert("There is an error in calculating assigntype 2 pay amounts.\n"+
                              "Report Error Assigntype 2\n"+
                              "to Dale Buchanan for correction.");
                        </script>
                    <?php                       
                    }
                    $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                }
                
                
                
                
                else if ($sqlq3['assigntype']==4) {
                    if (trim($sqlq3['assignment']) == 'Business' && $sqlmd['business']==0) {
                        $businessQH[$counterA] += ($sqlq3['endblock']-$sqlq3['beginblock']);
                    }
                    
                    else {
                        $sq1 = "SELECT payincrement, payincrement2 ".
                               "FROM assignments ".
                               "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                               "AND weekend={$sqlq3['weekend']} ".
                               "AND type_number=3";
                        $sq2 = mysql_query($sq1);
                        
                        
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement, payincrement2 FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                        if (!mysql_num_rows($sq2)) {
                                $sq1 = "SELECT payincrement, payincrement2 FROM assignments ".                                  
                                       "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                       "AND weekend={$sqlq3['weekend']} ".
                                       "AND ( ".
                                               "type_number = 2 ".
                                               "OR ".
                                               "type_number = 4 ".
                                               "OR ".
                                               "type_number = 6 ".
                                           ")";
                                $sq2 = mysql_query($sq1);
                        }
                        
                        if ($sq2 && (mysql_num_rows($sq2)==1)) {
                            $sq3 = mysql_fetch_array($sq2);
                        }
                        
                        
                        else {
                        ?>
                            <script type="text/javascript">
                            alert("There is an error in calculating assigntype 3 pay amounts.\n"+
                                  "Report Error Assigntype 3\n"+
                                  "to Dale Buchanan for correction.");
                            </script>
                        <?php                      
                        }
                        if ($sq3['payincrement2'] == 0.0){
                        //$pay[$counter] += (($sqlq3[3]-$sqlq3[2])*$sq3[0]);
                            $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                        }
                        else if ($sqlq3['endblock'] <= 54){
                            $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                        }
                        else if ($sqlq3['beginblock'] < 54) {
                            $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub(54,$sqlq3['beginblock']),$sq3['payincrement']));
                            $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'],54),$sq3['payincrement2']));
                        }
                        else{
                            $mdpay[$counterA] = bcadd($mdpay[$counterA],bcmul(bcsub($sqlq3['endblock'], $sqlq3['beginblock']),$sq3['payincrement2']));
                        }
                    }
                }                
            }
            

            
            
            
            /*
             * add administrative pay
             */
            $mdpay[$counterA] = bcadd($mdpay[$counterA],(bcmul($sqlmd['admin'],6)));
            
            
            
            /*
             * add business pay
             */
            $bpiq = "SELECT payincrement FROM assignments WHERE assignment LIKE ".
                    "'S Bus%'";
            $bpis = mysql_query($bpiq);
            if ($bpis) {
                $bpi = mysql_fetch_array($bpis);
            }
            else {            
            /*
            ?>
                <script type="text/javascript">
    		alert("There is an error in calculating business pay amounts.\n"+
                      "Please contact Dale Buchanan for correction.");
    		</script>
            <?php
             * 
             */
                 
            }
            /*
             * limit business hours to the maximum number at the top
             */
            if (isset($businessQH[$counterA]))
            {
                if ($businessQH[$counterA] > $max_bus_time_periods) {
                    $businessQH[$counterA] = $max_bus_time_periods;
                }
            }
            /*
             * if someone gets a business day, they cannot add business hours,
             * so zero out any business hours and then calculate the business
             * hours on the basis of how many business days they get
             */
            if ($sqlmd['business']>0 || $businessDayAssigned) {
                $businessQH[$counterA] = 0;
                $businessQH[$counterA] = $businessDQH[$counterA];
            }
            /*
             * add any business pay to total group pay and 
             * mdpay for this physician according to the business payincrement
             * and the individual's pay fraction
             */
            $businessPay[$counterA] = $businessQH[$counterA] * $bpi['payincrement'];
            $mdpay[$counterA] = bcadd($mdpay[$counterA], $businessPay[$counterA]);
            //$pay[$counter] += ($businessPay); 
            
            $mdpay[$counterA] *= $sqlmd['payfraction'];
            
            
            $totalpay += $mdpay[$counterA];
            $counterA++;
            $mdpay[$counterA] = 0.0;
            $businessQH[$counterA] = 0;
            $businessDQH[$counterA] = 0;
            $businessPay[$counterA] = 0.0;
        }
        
    
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        /*
         * STAGE 2:  Recalculates the same variables
         */
       
       

        include_once 'menuBar.php';
        menuBar(1567);
        
    echo '
          <h1 align="center">
              Pay Report For: '.$_REQUEST["mnt"].'/'.$_REQUEST["yar"].'
              </h1><br><br>

          <table class="tablepay">
                 <tr>
                    <th width="8%" align="center">
                    Number
                    </th>
                    <th width="17%" align="center">
                    Name
                    </th>
                    <th width="10%" align="center">
                    Initials
                    </th>
                    <th width="5%" align="center">
                    Pay Rate
                    </th>
                    <th width="10%" align="center">
                    Administrative Days
                    </th>
                    <th width="10%" align="center">
                    Business Hours
                    </th>
                    <th width="10%" align="center">
                    Gross Hours
                    </th>
                    <th width="10%" align="center">
                    Net Hours
                    </th>
                    <th width="10%" align="center">
                    Pay Fraction
                    </th>
                    <th width="10%" align="center">
                    Net Shifts
                    </th>
             </tr>
             ';

       
    
       
       
       
        /*
        * Cycle through each of the partners
        */
        $sqlq = "SELECT DISTINCT number, last, initials, payfraction, admin, business ".
                "FROM mds ".
                "WHERE number < 900 ".
                "ORDER BY number";
        $sqlqu = mysql_query($sqlq);
        
       /*
        * Initialize $x, $businessQH, $businessDQH
        */

        $x = 0;
        $counter = 0;
        
        $businessDQH2 = array();
        $businessQH2 = array();
        
        $businessQH2[0] = 0.0;
        $businessDQH2[0] = 0.0;
        $pay[0] = 0.0;
        
        /*
        * Cycle through each partner and calculate pay variables
        */
        while ($sqlmd = mysql_fetch_array($sqlqu)) {
            
            
            echo '
                   <tr>
                      <td>'.
                      $sqlmd['number'].'
                      </td>
                      <td>'.
                      $sqlmd['last'].'
                      </td>
                      <td>'.
                      $sqlmd['initials'].'
                      </td>
                      <td>'.
                      $sqlmd['payfraction'].'
                      </td>
                      <td>'.
                      $sqlmd['admin'].'
                      </td>';

            $sqlq1 = "SELECT assignment, weekend, beginblock, endblock, assigntype ".
                     "FROM monthassignment ".
                     "WHERE monthnumber={$_REQUEST['mnt']} ".
                     "AND yearnumber={$_REQUEST['yar']} ".
                     "AND mdnumber=$sqlmd[0] ".
                     "ORDER BY daynumber, beginblock";
            $sqlq2 = mysql_query($sqlq1);
            
            
            
            /*
             * Check to see if this partner has been assigned a 
             * 'S Bus' day during the month
             */
            
            $checkBD = "SELECT count(*) ".
                       "FROM monthassignment ".
                       "WHERE monthnumber={$_REQUEST['mnt']} ".
                       "AND yearnumber={$_REQUEST['yar']} ".
                       "AND mdnumber=$sqlmd[0] ".
                       "AND assignment LIKE 'S Bus%'";
            $checkBDQuery = mysql_query($checkBD);
            $checkBDResult = mysql_fetch_array($checkBDQuery);
            if ($checkBDResult[0] > 0) {
                $businessDayAssigned = true;
            }
            else {
                $businessDayAssigned = false;
            }
            
            
            
            /*
             * Cycle through each assignment for this partner for this month
             */

            while ($sqlq3 = mysql_fetch_array($sqlq2)) {
                
                    
                if ($sqlq3['assigntype']==1) {
                    /*
                     * keep track of the business days seperately
                     */
                    if (trim($sqlq3['assignment']) == 'S Bus') {
                        $businessDQH2[$counter] += ($sqlq3['endblock']-$sqlq3['beginblock']);
                    }
                    else if ((trim($sqlq3['assignment']) == 'Unwanted Vac') || (trim($sqlq3['assignment']) == 'Vac')
                            || (trim($sqlq3['assignment']) == 'UwVac') || (trim($sqlq3['assignment']) == 'None')
                            || (trim($sqlq3['assignment']) == 'Off') || (trim($sqlq3['assignment']) == 'Wkend')
                            || (trim($sqlq3['assignment']) == 'Hlday') || (trim($sqlq3['assignment']) == 'OhOff')
                            || (trim($sqlq3['assignment']) == 'ObOff') || (trim($sqlq3['assignment']) == 'OrOff')
                            || (trim($sqlq3['assignment']) == 'M Off') || (trim($sqlq3['assignment']) == 'H Off')
                            ) {
                    }
                    else {                                                    
                        $sq1 = "SELECT payincrement FROM assignments ".
                               "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                               "AND weekend={$sqlq3['weekend']} ".
                               "AND ( ".
                                       "type_number = 1 ".
                                       "OR ".
                                       "type_number = 4 ".
                                       "OR ".
                                       "type_number = 5 ".
                                   ")";
                           
                        $sq2 = mysql_query($sq1);
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 1 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 5 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                        
                        if ($sq2 && (mysql_num_rows($sq2)==1)) {
                           $sq3 = mysql_fetch_array($sq2);
                        }
                        else { 
                            echo $sq1.'<br>';
                        ?>
                            <script type="text/javascript">
                            alert("There is an error in calculating assigntype 1 pay amounts.\n"+
                                  "Report Error Assigntype 1\n"+
                                  "to Dale Buchanan for correction.");
                            </script>
                        <?php                             
                        }
                        //echo '<br>'.$sqlmd['number'].' - '.$sqlq3['assignment'].' - '. $sqlq3['weekend'].' - '.$sqlq3['beginblock'].' - '.$sqlq3['endblock'].' - '.$sqlq3['assigntype'].' - '.$sq3['payincrement'];
                        $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                    }
                }
                
                
                
                
                else if ($sqlq3['assigntype']==3) {
                    $sq1 = "SELECT payincrement FROM assignments ".
                           "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                           "AND weekend={$sqlq3['weekend']} ".
                           "AND ( ".
                                   "type_number = 2 ".
                                   "OR ".
                                   "type_number = 4 ".
                                   "OR ".
                                   "type_number = 6 ".
                               ")";
                    $sq2 = mysql_query($sq1);
                    
                    if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                     if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement FROM assignments ".                                  
                                   "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                    
                    
                    if ($sq2 && (mysql_num_rows($sq2)==1)) {
                        $sq3 = mysql_fetch_array($sq2);
                    }
                    else {
                    ?>
                        <script type="text/javascript">
                        alert("There is an error in calculating assigntype 2 pay amounts.\n"+
                              "Report Error Assigntype 2\n"+
                              "to Dale Buchanan for correction.");
                        </script>
                    <?php                       
                    }
                    //echo '<br>'.$sqlmd['number'].' - '.$sqlq3['assignment'].' - '. $sqlq3['weekend'].' - '.$sqlq3['beginblock'].' - '.$sqlq3['endblock'].' - '.$sqlq3['assigntype'].' - '.$sq3['payincrement'];
                    $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                }
                
                
                
                
                else if ($sqlq3['assigntype']==4) {
                    if (trim($sqlq3['assignment']) == 'Business' && $sqlmd['business']==0) {
                        $businessQH2[$counter] += ($sqlq3['endblock']-$sqlq3['beginblock']);
                    }
                    
                    else {
                        $sq1 = "SELECT payincrement, payincrement2 ".
                               "FROM assignments ".
                               "WHERE assignment LIKE '{$sqlq3['assignment']}' ".
                               "AND weekend={$sqlq3['weekend']} ".
                               "AND type_number=3";
                        $sq2 = mysql_query($sq1);
                        
                        
                        
                        if (!mysql_num_rows($sq2)) {
                            $sq1 = "SELECT payincrement, payincrement2 FROM assignments ".                                  
                                   "WHERE assignment LIKE trim('{$sqlq3['assignment']}') ".
                                   "AND weekend={$sqlq3['weekend']} ".
                                   "AND ( ".
                                           "type_number = 2 ".
                                           "OR ".
                                           "type_number = 4 ".
                                           "OR ".
                                           "type_number = 6 ".
                                       ")";
                            $sq2 = mysql_query($sq1);
                        }
                    
                    
                        if (!mysql_num_rows($sq2)) {
                                $sq1 = "SELECT payincrement, payincrement2 FROM assignments ".                                  
                                       "WHERE assignment LIKE (select rpad('{$sqlq3['assignment']}', 5, ' ')) ".
                                       "AND weekend={$sqlq3['weekend']} ".
                                       "AND ( ".
                                               "type_number = 2 ".
                                               "OR ".
                                               "type_number = 4 ".
                                               "OR ".
                                               "type_number = 6 ".
                                           ")";
                                $sq2 = mysql_query($sq1);
                        }
                        
                        if ($sq2 && (mysql_num_rows($sq2)==1)) {
                            $sq3 = mysql_fetch_array($sq2);
                        }
                        
                        
                        else {
                        ?>
                            <script type="text/javascript">
                            alert("There is an error in calculating assigntype 3 pay amounts.\n"+
                                  "Report Error Assigntype 3\n"+
                                  "to Dale Buchanan for correction.");
                            </script>
                        <?php                      
                        }
                        //$pay[$counter] += (($sqlq3[3]-$sqlq3[2])*$sq3[0]);
                        if ($sq3['payincrement2'] == 0.0){
                            $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                        }
                        else if ($sqlq3['endblock'] <= 54){
                            $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement']));
                        }
                        else if ($sqlq3['beginblock'] < 54){
                            $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub(54,$sqlq3['beginblock']),$sq3['payincrement']));
                            $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],54),$sq3['payincrement2']));
                        }
                        else{
                            $pay[$counter] = bcadd($pay[$counter],bcmul(bcsub($sqlq3['endblock'],$sqlq3['beginblock']),$sq3['payincrement2']));
                        }
                        //echo '<br>'.$sqlmd['number'].' - '.$sqlq3['assignment'].' - '. $sqlq3['weekend'].' - '.$sqlq3['beginblock'].' - '.$sqlq3['endblock'].' - '.$sqlq3['assigntype'].' - '.$sq3['payincrement'].' - '.$sq3['payincrement2'];
                    }
                }
            }

            
            
            
            /*
             * add administrative pay
             */
            $pay[$counter] = bcadd($pay[$counter],(bcmul($sqlmd['admin'],6)));
            
            
            
            /*
             * add business pay
             */
            $bpiq = "SELECT payincrement FROM assignments WHERE assignment LIKE ".
                    "'S Bus%'";
            $bpis = mysql_query($bpiq);
            if ($bpis) {
                $bpi = mysql_fetch_array($bpis);
            }
            else {
            
            /*
            ?>
                <script type="text/javascript">
    		alert("There is an error in calculating business pay amounts.\n"+
                      "Please contact Dale Buchanan for correction.");
    		</script>
            <?php
             * 
             */
                 
            }
            /*
             * limit business hours to the maximum number at the top
             */
            if (isset($businessQH2[$counter]))
            {
                if ($businessQH2[$counter] > $max_bus_time_periods) {
                    $businessQH2[$counter] = $max_bus_time_periods;
                }
            }
            /*
             * if someone gets a business day, they cannot add business hours,
             * so zero out any business hours and then calculate the business
             * hours on the basis of how many business days they get
             */
            if ($sqlmd['business']>0 || $businessDayAssigned) {
                $businessQH2[$counter] = 0;
                $businessQH2[$counter] = $businessDQH2[$counter];
            }
            /*
             * add any business pay to total group pay and 
             * mdpay for this physician according to the business payincrement
             * and the individual's pay fraction
             */
            $businessPay[$counter] = $businessQH2[$counter] * $bpi['payincrement'];
            $pay[$counter] = bcadd($pay[$counter], $businessPay[$counter]);
            //$pay[$counter] += ($businessPay); 
            
            
            
           
            $grosspay[$counter] = $pay[$counter];

            
            
            
            
            
            
            
            
       for ($x=0; $x<strlen($grosspay[$counter]); $x++)
       {
           if (strcmp(substr($grosspay[$counter],$x,1),'.')==0)
                  $dot = $x;
       }
       if (!isset($dot))
       {
           $dot = "NO";
       }
       else 
       {
           if ((strcmp(substr($grosspay[$counter],$dot+4,1),'8')==0) && (strcmp(substr($grosspay[$counter],$dot+3,1),'9')==0))
           {
               $grosspay[$counter] += .0002;
               $grosspay[$counter] = (floor(($grosspay[$counter]) * pow(10,4))/pow(10,4));
           }
           else if (strcmp(substr($grosspay[$counter],$dot+4,1),'9')==0)
           {
               $grosspay[$counter] += .0001;
               $grosspay[$counter] = (floor(($grosspay[$counter]) * pow(10,4))/pow(10,4));
           }
       }
       $dot = null;


       $busHours = $businessQH2[$counter]/4;
       echo '
            <td width="100" align="right">'.
            $busHours.'</td>';

       $pay[$counter]=$grosspay[$counter];
       $pay[$counter]*=$sqlmd['payfraction'];

       $secondTotalPay += $pay[$counter];

       $total_pay_fraction = $pay[$counter]/$totalpay;
       $tpf_formatted = number_format($total_pay_fraction, 5, '.',',');
       $shifts = $pay[$counter]/8;
       $shifts_formatted = number_format($shifts, 5, '.', ',');
       echo '<td width="100" align="right">';
       printf("%7.4f", $grosspay[$counter]);

      echo '
              </td>
              <td width="100" align="right">';
              printf ("%7.4f",$pay[$counter]);

      echo'
              </td>
              <td width="100" align="right">'.
              $tpf_formatted.'
              </td>
                  <td width="100" align="right">'.
              $shifts_formatted.'
              </td>
           </tr>
       ';


       $counter++;
       $pay[$counter]=0.0;
       $businessDQH2[$counter] = 0.0;
       $businessQH2[$counter] = 0.0;
       $businessPay[$counter] = 0.0;
       }
       echo'
            </table>
            <table class = "bot">
               <tr>
                  <td align="middle">
                          Net Total Hours For Month = '.number_format($secondTotalPay,4,".",",").'
                          </td>
               </tr>
            </table>
               ';
        
        echo '  <br><br><br><br><h2 align="center">Reasons for Adding Extra'.
                ' Business, Meeting, & Other Hours</h2>
                
                <table class="tablepay" align="center">
                <tr><th width="10%">Initials</th><th width="10%">MD Number</th>
                <th width="10%">Day</th>
                <th>Comments</th></tr>';
        $ADReason = "SELECT * ".
                    "FROM ahr ".
                    "WHERE dtm = ".$_REQUEST["mnt"].' AND dty ='.$_REQUEST["yar"].' '.
                    "ORDER BY mdn, dai";

        $ADReasonQuery = mysql_query($ADReason);
        while ($ADReasonResult = mysql_fetch_array($ADReasonQuery)) {
            echo '<tr><td>'.$ADReasonResult['initials'].'</td><td>'.$ADReasonResult['mdn'].
                 '</td><td>'.$ADReasonResult['dai'].'</td><td>'.$ADReasonResult['commnt'].'</td></tr>';
    } 
    }
}
?>

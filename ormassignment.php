<?php
session_start();
    
require_once ('connect2.php');

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
    require_once ($_SESSION['login2string']);
    
    unset($_SESSION['meeting']);

   
    echo'
    <link rel="stylesheet" href="style2.css" type="text/css">
    ';
    
    echo '<title>ORMGR Assignment Page</title>';
    
    /*
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'ormpage.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
     * 
     */

    echo'
        <body><center>';
    
    
    include_once 'menuBar3.php';
    menuBar3(1663);
    
    
    
    
    echo '
        <br><br>
	<center><zz>OR Manager Assignment Sheet</zz><br><br>';
    
    
    if (isset($_POST['month']))
    {
        $month = $_POST['month'];
        $day = $_POST['day'];
        $year = $_POST['year'];
    }
    
    $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    if ($day > $number)
    {
        echo'
            <form method="post" action="ormassignmentpre.php" class="input">
            <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display=\'none\';"></span> 
            <strong>Error!</strong> The date does not exist. There are not that many days in that month.  Press SUBMIT to start over.
            </div>
            <br><br><br>
        
        <table align="center" width="100%" style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px; border:none">
            <tr>
                <td width="50%" align="center" style="border: none">
                     <input type="submit" name="ORMA" value="SUBMIT" class="btn">
                </td>
                </form>
            </tr>
        </table>';

    }
    else 
    {
    
        $datetime = new DateTime('now');
    
        if (isset($month) && isset($day) && isset($year))
            $datetime->setDate($year, $month, $day);
        else 
        {
            $datetime->modify(("+1 days"));
        }
    
        $_SESSION['d'] = $datetime->format('d');
        $_SESSION['m'] = $datetime->format('m');
        $_SESSION['y'] = $datetime->format('Y');


        /*
         * Set up the table to display the results.  Create a row of dates across the top.
         */

        echo '
            <table>
                <tr>
                    <th>For: '.$datetime->format('l').', '.
                        $datetime->format('j').' '.$datetime->format('F').' '. $datetime->format('Y').'
                    </th>
                </tr>
            </table>
            <br><br>


        <form method="post" action="ormassignmentxml.php" class="input">';
        
         echo '
            <table style="background-color:#FFFF66;">
                <tr>
                    <td width=20%><b>ASSIGNMENT CHANGES: </b></td>
                    <td width=80%>
                        <textarea rows="3" cols="80" name="oanote">Changes:</textarea>
                    </td>
                </tr>
            </table><br><br>';

        echo '
            <table style="background-color:#F5F5C8;">
                <tr>
                    <td width=20%><b>Presbyterian Main: </b></td>
                    <td width=20%><b>Anesthesiologist</b></td>
                    <td width=20%><b>Rooms</b></td>
                    <td width=40%><b>Notes</b></td>
                </tr>
                <tr>
                    <td width=20%>ORMGR</td>
                    <td width=20%>';

        $ormgra = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'ORMGR%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $ormgraQuery = mysql_query($ormgra);
        
        $x=0;
        while ($ormgrText = mysql_fetch_array($ormgraQuery))
        {
            echo $ormgrText['First'].' '.$ormgrText['Last'].'<br>';
            $_SESSION['ormgr'][$x] = $ormgrText['First'].' '.$ormgrText['Last'];
            $x++;
        }
        $x=0;
        
        echo'
                    </td>
                    <td width=20%>
                        <table style="border-style: solid;">
                            <tr>
                                <td>
                    <b>ECTs:</b>
                                </td>
                                <td style="border:none">                    
                    <select name="ect">
                        <option selected value=0>0</option>
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                    </select>
                                </td>
                            </tr>
                        </table>
                        <table style="border-style: solid;">
                            <tr>
                                <td>
                    <b>Jutras:</b>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="nine" name="jutras[]"
                        value="0900" />
                    <label for="nine">0900</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="ninet" name="jutras[]"
                        value="0930" />
                    <label for="ninet">0930</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="ten" name="jutras[]"
                        value="0900" />
                    <label for="ten">1000</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="tent" name="jutras[]"
                        value="1030" />
                    <label for="tent">1030</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="eleven" name="jutras[]"
                        value="1100" />
                    <label for="eleven">1100</label>
                            </tr>
                        </table>';
                    
/**                    <select name="jutras">
                        <option value="None">None</option>
                        <option value="0900">0900</option>
                        <option value="0930">0930</option>
                        <option value="1000">1000</option>
                        <option value="1030">1030</option>
                        <option value="1100">1100</option>
                    </select>
 * 
 */
                    

echo'
                    </td>
                    <td width=40%>
                        <textarea rows="3" cols="50" name="ormgrnote"></textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>SLate</td>
                    <td width=20%>';

        $slatea = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'SLate%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $slateaQuery = mysql_query($slatea);

        while ($slateText = mysql_fetch_array($slateaQuery))
        {
            echo $slateText['First'].' '.$slateText['Last'].'<br>';
            $_SESSION['slate'][$x] = $slateText['First'].' '.$slateText['Last'];
            $x++;
        }
        $x=0;

        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px">
                    <table>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A1" name="slate[]"
                        value="A1" checked />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A2" name="slate[]"
                        value="A2" checked/>
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A3" name="slate[]"
                        value="A3" />
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A4" name="slate[]"
                        value="A4" />
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                                        
                    <input type="checkbox" id="A5" name="slate[]"
                        value="A5" />
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A6" name="slate[]"
                        value="A6" />
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A7" name="slate[]"
                        value="A7" checked/>
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B1" name="slate[]"
                        value="B1" checked/>
                    <label for="B1">B1</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="B2" name="slate[]"
                        value="B2" />
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B3" name="slate[]"
                        value="B3" />
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B4" name="slate[]"
                        value="B4" />
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B5" name="slate[]"
                        value="B5" />
                    <label for="B5">B5</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                                        
                    <input type="checkbox" id="B6" name="slate[]"
                        value="B6" />                        
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B7" name="slate[]"
                        value="B7" />
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B8" name="slate[]"
                        value="B8" />
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B9" name="slate[]"
                        value="B9" />
                    <label for="B9">B9</label>
                                </td>
                            </tr>
                        </table>
                    </td>';
                           
/**                   
                      <select multiple="multiple" size="3" name="slate[]">
                      <option selected value="A1">A1</option>
                      <option selected value="A2">A2</option>
                      <option value="A3">A3</option>
                      <option value="A4">A4</option>
                      <option value="A5">A5</option>
                      <option value="A6">A6</option>
                      <option selected value="A7">A7</option>
                      <option selected value="B1">B1</option>
                      <option value="B2">B2</option>
                      <option value="B3">B3</option>
                      <option value="B4">B4</option>
                      <option value="B5">B5</option>
                      <option value="B6">B6</option>
                      <option value="B7">B7</option>
                      <option value="B8">B8</option>
                      <option value="B9">B9</option>
                    </select>
                    </td>
 * 
 */
        echo'
                    <td width=40%>
                        <textarea rows="3" cols="50" name="slatenote"></textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>Neuro</td>
                    <td width=20%>';
        $neuroa = "SELECT First, Last FROM mds AS a ".
                   "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                   "WHERE assignment LIKE 'Neuro%' ".
                   "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                   " AND b.yearnumber=".$datetime->format('Y');

        $neuroaQuery = mysql_query($neuroa);

        while ($neuroaText = mysql_fetch_array($neuroaQuery))
        {
            echo $neuroaText['First'].' '.$neuroaText['Last'].'<br>';
            $_SESSION['neuro'][$x] = $neuroaText['First'].' '.$neuroaText['Last'];
            $x++;
        }
        $x=0;
        
        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px">
                    <table>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A1" name="neuro[]"
                        value="A1" />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A2" name="neuro[]"
                        value="A2" />
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A3" name="neuro[]"
                        value="A3" checked/>
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A4" name="neuro[]"
                        value="A4" checked/>
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="A5" name="neuro[]"
                        value="A5" checked/>
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A6" name="neuro[]"
                        value="A6" checked/>
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A7" name="neuro[]"
                        value="A7" />
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B1" name="neuro[]"
                        value="B1" />
                    <label for="B1">B1</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="B2" name="neuro[]"
                        value="B2" />
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B3" name="neuro[]"
                        value="B3" />
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B4" name="neuro[]"
                        value="B4" />
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B5" name="neuro[]"
                        value="B5" />
                    <label for="B5">B5</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="B6" name="neuro[]"
                        value="B6" />
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B7" name="neuro[]"
                        value="B7" />
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B8" name="neuro[]"
                        value="B8" />
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B9" name="neuro[]"
                        value="B9" />
                    <label for="B9">B9</label>
                                </td>
                            </tr>
                        </table>';
        /**
        echo '
                    </td>
                    <td width=20%>
                      <select multiple="multiple" size="3" name="neuro[]">
                      <option value="A1">A1</option>
                      <option value="A2">A2</option>
                      <option selected value="A3">A3</option>
                      <option selected value="A4">A4</option>
                      <option selected value="A5">A5</option>
                      <option selected value="A6">A6</option>
                      <option value="A7">A7</option>
                      <option value="B1">B1</option>
                      <option value="B2">B2</option>
                      <option value="B3">B3</option>
                      <option value="B4">B4</option>
                      <option value="B5">B5</option>
                      <option value="B6">B6</option>
                      <option value="B7">B7</option>
                      <option value="B8">B8</option>
                      <option value="B9">B9</option>
                      </select></td>
         * 
         */
        echo'
                    <td width=40%>
                        <textarea rows="3" cols="50" name="neuronote"></textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>BLong</td>
                    <td width=20%>';
        
        $blonga = "SELECT First, Last FROM mds AS a ".
                   "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                   "WHERE assignment LIKE 'BLong%' ".
                   "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                   " AND b.yearnumber=".$datetime->format('Y');

        $blongaQuery = mysql_query($blonga);

        while ($blongaText = mysql_fetch_array($blongaQuery))
        {
            echo $blongaText['First'].' '.$blongaText['Last'].'<br>';
            $_SESSION['blong'][$x] = $blongaText['First'].' '.$blongaText['Last'];
            $x++;
        }
        $x=0;
        
        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px">
                    <table>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A1" name="blong[]"
                        value="A1" />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A2" name="blong[]"
                        value="A2" />
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A3" name="blong[]"
                        value="A3" />
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A4" name="blong[]"
                        value="A4" />
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A5" name="blong[]"
                        value="A5" />
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A6" name="blong[]"
                        value="A6" />
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A7" name="blong[]"
                        value="A7" />
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B1" name="blong[]"
                        value="B1" />
                    <label for="B1">B1</label><br>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                                
                    <input type="checkbox" id="B2" name="blong[]"
                        value="B2" checked/>
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B3" name="blong[]"
                        value="B3" checked/>
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B4" name="blong[]"
                        value="B4" checked/>
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B5" name="blong[]"
                        value="B5" checked/>
                    <label for="B5">B5</label><br>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                            
                    <input type="checkbox" id="B6" name="blong[]"
                        value="B6" />
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B7" name="blong[]"
                        value="B7" />
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B8" name="blong[]"
                        value="B8" />
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B9" name="blong[]"
                        value="B9" />
                    <label for="B9">B9</label><br></td>
                                </td>
                            </tr>
                        </table>
                    </td>';
        /*
        
        echo '</td>
                    <td width=20%>
                      <select multiple="multiple" size="3" name="blong[]">
                      <option value="A1">A1</option>
                      <option value="A2">A2</option>
                      <option value="A3">A3</option>
                      <option value="A4">A4</option>
                      <option value="A5">A5</option>
                      <option value="A6">A6</option>
                      <option value="A7">A7</option>
                      <option value="B1">B1</option>
                      <option selected value="B2">B2</option>
                      <option selected value="B3">B3</option>
                      <option selected value="B4">B4</option>
                      <option selected value="B5">B5</option>
                      <option value="B6">B6</option>
                      <option value="B7">B7</option>
                      <option value="B8">B8</option>
                      <option value="B9">B9</option>
                      </select></td>
         * 
         */
        echo'
                    <td width=40%>
                        <textarea rows="3" cols="50" name="blongnote"></textarea>
                    </td>
                </tr>



                <tr>
                    <td width=20%>S_B</td>
                    <td width=20%>';

        $sba =    "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'S_B%' AND assignment NOT LIKE 'S_Bus' AND assignment NOT LIKE 'SOBA%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $sbaQuery = mysql_query($sba);

        while ($sbaText = mysql_fetch_array($sbaQuery))
        {
            echo $sbaText['First'].' '.$sbaText['Last'].'<br>';
            $_SESSION['sb'][$x] = $sbaText['First'].' '.$sbaText['Last'];
            $x++;
        }
        $x=0;
        
        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px;">
                        <table>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A1" name="sb[]"
                        value="A1" />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A2" name="sb[]"
                        value="A2" />
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A3" name="sb[]"
                        value="A3" />
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A4" name="sb[]"
                        value="A4" />
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                    
                    <input type="checkbox" id="A5" name="sb[]"
                        value="A5" />
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A6" name="sb[]"
                        value="A6" />
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="A7" name="sb[]"
                        value="A7" />
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B1" name="sb[]"
                        value="B1" />
                    <label for="B1">B1</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="B2" name="sb[]"
                        value="B2" />
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B3" name="sb[]"
                        value="B3" />
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B4" name="sb[]"
                        value="B4" />
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B5" name="sb[]"
                        value="B5" />
                    <label for="B5">B5</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">                                       
                    <input type="checkbox" id="B6" name="sb[]"
                        value="B6" checked/>
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B7" name="sb[]"
                        value="B7" checked/>
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B8" name="sb[]"
                        value="B8" checked/>
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">
                    <input type="checkbox" id="B9" name="sb[]"
                        value="B9" checked/>
                    <label for="B9">B9</label><br>
                                </td>
                            </tr>
                        </table>';
        /*

        echo'</td>
                    <td width=20%>
                      <select multiple="multiple" size="3" name="sb[]">
                      <option value="A1">A1</option>
                      <option value="A2">A2</option>
                      <option value="A3">A3</option>
                      <option value="A4">A4</option>
                      <option value="A5">A5</option>
                      <option value="A6">A6</option>
                      <option value="A7">A7</option>
                      <option value="B1">B1</option>
                      <option value="B2">B2</option>
                      <option value="B3">B3</option>
                      <option value="B4">B4</option>
                      <option value="B5">B5</option>
                      <option selected value="B6">B6</option>
                      <option selected value="B7">B7</option>
                      <option selected value="B8">B8</option>
                      <option selected value="B9">B9</option>
                      </select></td>
         * 
         */
        echo'
                    <td width=40%>
                        <textarea rows="3" cols="50" name="sbnote"></textarea>
                    </td>
                </tr>


            <tr>
                <td width=20%>OB_1</td>
                <td width=20%>';

        $oba =    "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'OB_1%' AND assignment NOT LIKE 'S_Bus'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $obaQuery = mysql_query($oba);

        while ($obText = mysql_fetch_array($obaQuery))
        {
            echo $obText['First'].' '.$obText['Last'].'<br>';
            $_SESSION['ob1'][$x] = $obText['First'].' '.$obText['Last'];
            $x++;
        }
        $x=0;

        echo'</td>
             <td width=20%></td>
                    <td width=40% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="50" name="obnote"></textarea>
                    </td>
                </tr>

                <tr>
                    <td width=20%>SEndo</td>
                    <td width=20%>';

        $sendoa =    "SELECT First, Last FROM mds AS a ".
                     "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                     "WHERE assignment LIKE 'SEndo%'".
                     "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                     " AND b.yearnumber=".$datetime->format('Y');

        $sendoaQuery = mysql_query($sendoa);

        while ($sendoText = mysql_fetch_array($sendoaQuery))
        {
            echo $sendoText['First'].' '.$sendoText['Last'].'<br>';
            $_SESSION['endo'][$x] = $sendoText['First'].' '.$sendoText['Last'];
            $x++;
        }
        $x=0;

        echo'</td>
             <td width=20%></td>
                    <td width=40% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="50" name="sendonote"></textarea>
                    </td>
                </tr>


                <tr>
                    <td width=20%>S_Rad</td>
                    <td width=20%>';

        $srada  =    "SELECT First, Last FROM mds AS a ".
                     "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                     "WHERE assignment LIKE 'S_Rad%' AND assignment NOT LIKE 'S_Bus'".
                     "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                     " AND b.yearnumber=".$datetime->format('Y');

        $sradaQuery = mysql_query($srada);

        while ($sradText = mysql_fetch_array($sradaQuery))
        {
            echo $sradText['First'].' '.$sradText['Last'].'<br>';
            $_SESSION['srad'][$x] = $sradText['First'].' '.$sradText['Last'];
            $x++;
        }
        $x=0;
        
        echo'</td>
             <td width=20%></td>
                    <td width=40% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="50" name="sradnote"></textarea>
                    </td>
                </tr>

                <tr>
                    <td width=20%>Pediatric Coverage</td>
                    <td width=20%>';

        $peda  =    "SELECT First, Last FROM mds AS a ".
                     "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                     "WHERE assignment LIKE '%/Ped%' AND assignment NOT LIKE '%Peds Call%'".
                     "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                     " AND b.yearnumber=".$datetime->format('Y');
        

        $pedaQuery = mysql_query($peda);

        while ($pedText = mysql_fetch_array($pedaQuery))
        {
            echo $pedText['First'].' '.$pedText['Last'].'<br>';
            $_SESSION['ped'][$x] = $pedText['First'].' '.$pedText['Last'];
            $x++;
        }
        $x=0;
        
        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px">
                        <table>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A1" name="ped[]"
                        value="A1" />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A2" name="ped[]"
                        value="A2" />
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A3" name="ped[]"
                        value="A3" />
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A4" name="ped[]"
                        value="A4" />
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A5" name="ped[]"
                        value="A5" />
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A6" name="ped[]"
                        value="A6" />
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A7" name="ped[]"
                        value="A7" />
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B1" name="ped[]"
                        value="B1" />
                    <label for="B1">B1</label>                            
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="B2" name="ped[]"
                        value="B2" />
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B3" name="ped[]"
                        value="B3" />
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B4" name="ped[]"
                        value="B4" />
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B5" name="ped[]"
                        value="B5" />
                    <label for="B5">B5</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="B6" name="ped[]"
                        value="B6" />
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B7" name="ped[]"
                        value="B7" />
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B8" name="ped[]"
                        value="B8" />
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B9" name="ped[]"
                        value="B9" />
                    <label for="B9">B9</label>
                                </td>
                            </tr>
                        </table>                    
                    </td>';
        /*

        echo'       </td>
                    <td width=20%>
                      <select multiple="multiple" size="3" name="ped[]">
                      <option value="A1">A1</option>
                      <option value="A2">A2</option>
                      <option value="A3">A3</option>
                      <option value="A4">A4</option>
                      <option value="A5">A5</option>
                      <option value="A6">A6</option>
                      <option value="A7">A7</option>
                      <option value="B1">B1</option>
                      <option value="B2">B2</option>
                      <option value="B3">B3</option>
                      <option value="B4">B4</option>
                      <option value="B5">B5</option>
                      <option value="B6">B6</option>
                      <option value="B7">B7</option>
                      <option value="B8">B8</option>
                      <option value="B9">B9</option>
                      </select></td>
         * 
         */
        echo'
                    <td width=40%>
                        <textarea rows="3" cols="50" name="pednote"></textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>ERAS</td>
                    <td width=20%>';

        $erasa =    "SELECT First, Last FROM mds AS a ".
                    "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                    "WHERE assignment LIKE 'ERAS%' AND assignment NOT LIKE 'S_Bus'".
                    "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                    " AND b.yearnumber=".$datetime->format('Y');

        $erasaQuery = mysql_query($erasa);

        if (@mysql_num_rows($erasaQuery) == FALSE)
        {
            echo 'None ';
        }
        else
        {        
            while ($erasText = mysql_fetch_array($erasaQuery))
            {
                echo $erasText['First'].' '.$erasText['Last'].'<br>';
                $_SESSION['eras'][$x] = $erasText['First'].' '.$erasText['Last'];
                $x++;
            }
        }
        $x=0;

        echo'
                    </td>
                    <td style="padding-top:15px; padding-bottom:15px">
                        <table>
                            <tr>
                                <td style="border:none">                            
                    <input type="checkbox" id="A1" name="eras[]"
                        value="A1" />
                    <label for="A1">A1</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A2" name="eras[]"
                        value="A2" />
                    <label for="A2">A2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A3" name="eras[]"
                        value="A3" />
                    <label for="A3">A3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A4" name="eras[]"
                        value="A4" />                    
                    <label for="A4">A4</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="A5" name="eras[]"
                        value="A5" />
                    <label for="A5">A5</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A6" name="eras[]"
                        value="A6" />
                    <label for="A6">A6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="A7" name="eras[]"
                        value="A7" />
                    <label for="A7">A7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B1" name="eras[]"
                        value="B1" />
                    <label for="B1">B1</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="B2" name="eras[]"
                        value="B2" />
                    <label for="B2">B2</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B3" name="eras[]"
                        value="B3" />
                    <label for="B3">B3</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B4" name="eras[]"
                        value="B4" />
                    <label for="B4">B4</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B5" name="eras[]"
                        value="B5" />
                    <label for="B5">B5</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none">
                    <input type="checkbox" id="B6" name="eras[]"
                        value="B6" />
                    <label for="B6">B6</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B7" name="eras[]"
                        value="B7" />
                    <label for="B7">B7</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B8" name="eras[]"
                        value="B8" />
                    <label for="B8">B8</label>
                                </td>
                                <td style="border:none">                    
                    <input type="checkbox" id="B9" name="eras[]"
                        value="B9" />
                    <label for="B9">B9</label>
                            </tr>
                        </table>                    
                    </td>';
        /*

        echo'
                </td>
                <td width=20%>
                  <select multiple="multiple" size="3" name="eras[]">
                  <option value="A1">A1</option>
                  <option value="A2">A2</option>
                  <option value="A3">A3</option>
                  <option value="A4">A4</option>
                  <option value="A5">A5</option>
                  <option value="A6">A6</option>
                  <option value="A7">A7</option>
                  <option value="B1">B1</option>
                  <option value="B2">B2</option>
                  <option value="B3">B3</option>
                  <option value="B4">B4</option>
                  <option value="B5">B5</option>
                  <option value="B6">B6</option>
                  <option value="B7">B7</option>
                  <option value="B8">B8</option>
                  <option value="B9">B9</option>
                  </select></td>
         * 
         */
        echo'
                <td width=40%>
                    <textarea rows="3" cols="50" name="erasnote"></textarea>
                </td>
            </tr>';
        
        echo'
        </table><br><br>';




////////////////////////////////////////////////////////////////////////////////
//CVOR


        $h1a    = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'H_1%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $h2a    = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'H_2%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $h1Query = mysql_query($h1a);
        $h2Query = mysql_query($h2a);



        echo '
            <br><br>
            <table style="background-color:#C23E33;">
                <tr>
                    <td width=20%><b>CVOR: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>H 1:  </b>';

                    while ($h1Text = mysql_fetch_array($h1Query))
                    {
                        echo $h1Text['First'].' '.$h1Text['Last'].'<br><br>';
                        $_SESSION['h1d'][$x] = $h1Text['First'].' '.$h1Text['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>H 2:  </b>';
                    while ($h2Text = mysql_fetch_array($h2Query))
                    {
                        echo $h2Text['First'].' '.$h2Text['Last'];
                        $_SESSION['h2'][$x] = $h2Text['First'].' '.$h2Text['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="cvornote">CVOR Notes: </textarea>
                    </td>
                </tr>
            </table>';    





    ////////////////////////////////////////////////////////////////////////////////
    //Orthopedic    
        $coha   = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'C_OH%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $soha   = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'S_OH%' AND assignment NOT LIKE 'S_OH2%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $soh2a  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'S_OH2%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $bkoha  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'BK_OH%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $cohaQuery = mysql_query($coha);
        $sohQuery = mysql_query($soha);
        $soh2Query = mysql_query($soh2a);
        $bkohQuery = mysql_query($bkoha);

        echo '
            <br><br>
            <table style="background-color:#50AD39;">
                <tr>
                    <td width=20%><b>Orthopedic: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C OH:  </b>';

                    while ($cohText = mysql_fetch_array($cohaQuery))
                    {
                        echo $cohText['First'].' '.$cohText['Last'].'<br>';
                        $_SESSION['cohd'][$x] = $cohText['First'].' '.$cohText['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <br><b>S OH:  </b>';
                    while ($sohText = mysql_fetch_array($sohQuery))
                    {
                        echo $sohText['First'].' '.$sohText['Last'].'<br>';
                        $_SESSION['soh'][$x] = $sohText['First'].' '.$sohText['Last'];
                        $x++;
                    }
                    $x=0;

        echo'              
                    <br><b>S OH2:  </b>';
                    while ($soh2Text = mysql_fetch_array($soh2Query))
                    {
                        echo $soh2Text['First'].' '.$soh2Text['Last'].'<br>';
                        $_SESSION['soh2'][$x] = $soh2Text['First'].' '.$soh2Text['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <br><b>BK OH:  </b>';
                    while ($bkohText = mysql_fetch_array($bkohQuery))
                    {
                        echo $bkohText['First'].' '.$bkohText['Last'].'<br>';
                        $_SESSION['bkoh'][$x] = $bkohText['First'].' '.$bkohText['Last'];
                        $x++;
                    }
                    $x=0;
        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="7" cols="80" name="orthonote">Ortho Notes: </textarea>
                    </td>
                </tr>
            </table>';       



////////////////////////////////////////////////////////////////////////////////
//CLTOPS

        $cops1a = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'COPS1%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $cops2a = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'COPS2%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $cops1aQuery = mysql_query($cops1a); 
        $cops2aQuery = mysql_query($cops2a);


        echo '
            <br><br>
            <table style="background-color:#E1CCCC;">
                <tr>
                    <td width=20%><b>CLTOPS: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>Ops1:  </b>';

                    while ($cops1Text = mysql_fetch_array($cops1aQuery))
                    {
                        echo $cops1Text['First'].' '.$cops1Text['Last'].'<br>';
                        $_SESSION['cops1'][$x] = $cops1Text['First'].' '.$cops1Text['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>Ops2:  </b>';
                    while ($cops2Text = mysql_fetch_array($cops2aQuery))
                    {
                        echo $cops2Text['First'].' '.$cops2Text['Last'].'<br>';
                        $_SESSION['cops2'][$x] = $cops2Text['First'].' '.$cops2Text['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="opsnote">CLTOPS Notes: </textarea>
                    </td>
                </tr>
            </table>';    


////////////////////////////////////////////////////////////////////////////////
//Midtown

        $mid1a  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'MidTn%' AND assignment NOT LIKE 'MidTn2%'".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $mid2a  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'MidTn2%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $mid1aQuery = mysql_query($mid1a); 
        $mid2aQuery = mysql_query($mid2a);


        echo '
            <br><br>
            <table style="background-color:#C46d66;">
                <tr>
                    <td width=20%><b>Midtown: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>MidTn:  </b>';

                    while ($mid1Text = mysql_fetch_array($mid1aQuery))
                    {
                        echo $mid1Text['First'].' '.$mid1Text['Last'].'<br><br>';
                        $_SESSION['mid1'][$x] = $mid1Text['First'].' '.$mid1Text['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>MidTn2:  </b>';
                    while ($mid2Text = mysql_fetch_array($mid2aQuery))
                    {
                        echo $mid2Text['First'].' '.$mid2Text['Last'];
                        $_SESSION['mid2'][$x] = $mid2Text['First'].' '.$mid2Text['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="midnote">Midtown Notes: </textarea>
                    </td>
                </tr>
            </table>'; 



////////////////////////////////////////////////////////////////////////////////
//Southpark

        $spka = "SELECT First, Last FROM mds AS a ".
                "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                "WHERE assignment LIKE 'Spk%' AND assignment NOT LIKE 'Spk 2%'".
                "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                " AND b.yearnumber=".$datetime->format('Y');
        $spk2a= "SELECT First, Last FROM mds AS a ".
                "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                "WHERE assignment LIKE 'Spk 2%'".
                "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                " AND b.yearnumber=".$datetime->format('Y');

         $spkaQuery = mysql_query($spka);
         $spk2aQuery = mysql_query($spk2a);


         echo '
            <br><br>
            <table style="background-color:#507abf;">
                <tr>
                    <td width=20%><b>Southpark: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>Spk:  </b>';

                    while ($spkText = mysql_fetch_array($spkaQuery))
                    {
                        echo $spkText['First'].' '.$spkText['Last'].'<br><br>';
                        $_SESSION['spk'][$x] = $spkText['First'].' '.$spkText['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>Spk2:  </b>';
                    while ($spk2Text = mysql_fetch_array($spk2aQuery))
                    {
                        echo $spk2Text['First'].' '.$spk2Text['Last'];
                        $_SESSION['spk2'][$x] = $spk2Text['First'].' '.$spk2Text['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="spknote">SouthPark Notes: </textarea>
                    </td>
                </tr>
            </table>';




        ////////////////////////////////////////////////////////////////////////////
        //MATTHEWS

        $cmata  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'C_Mat%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $smla   = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'SMLate%' AND assignment NOT LIKE 'S_OH2%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');
        $smata  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'S Mat%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $cmataQuery = mysql_query($cmata);
        $smlQuery = mysql_query($smla);
        $smatQuery = mysql_query($smata);


        echo '
            <br><br>
            <table style="background-color:#DDFFFF;">
                <tr>
                    <td width=20%><b>Matthews: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C Mat:  </b>';

                    while ($cmatText = mysql_fetch_array($cmataQuery))
                    {
                        echo $cmatText['First'].' '.$cmatText['Last'].'<br><br>';
                        $_SESSION['cmatd'][$x] = $cmatText['First'].' '.$cmatText['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>SMLate:  </b>';
                    while ($smlText = mysql_fetch_array($smlQuery))
                    {
                        echo $smlText['First'].' '.$smlText['Last'].'<br><br>';
                        $_SESSION['sml'][$x] = $smlText['First'].' '.$smlText['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    <b>S Mat:  </b>';
                    while ($smatText = mysql_fetch_array($smatQuery))
                    {
                        echo $smatText['First'].' '.$smatText['Last'];
                        $_SESSION['smat'][$x] = $smatText['First'].' '.$smatText['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="mattnote">Matthews Notes: </textarea>
                    </td>
                </tr>
            </table>';


////////////////////////////////////////////////////////////////////////////////
//MASC

        $masca  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'MASC%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $mascaQuery = mysql_query($masca);

        echo '
            <br><br>
            <table style="background-color:#EEFFFF;">
                <tr>
                    <td width=20%><b>MASC: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        while ($mascText = mysql_fetch_array($mascaQuery))
        {
            echo $mascText['First'].' '.$mascText['Last'].'<br>';
            $_SESSION['masc'][$x] = $mascText['First'].' '.$mascText['Last'];
            $x++;
        }
        $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="mascnote">MASC Notes: </textarea>
                    </td>
                </tr>
            </table>';



////////////////////////////////////////////////////////////////////////////////
//Huntersville


        $chnta  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'C_Hnt%' ".
                  "AND assigntype=1 ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $shnta  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'S Hnt%' AND assignment NOT LIKE 'S_OH2%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $shnt2a = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'Shnt2%'".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $chntaQuery = mysql_query($chnta);
        $shntQuery = mysql_query($shnta);
        $shnt2Query = mysql_query($shnt2a);

        echo '
            <br><br>
            <table style="background-color:#AAFF99;">
                <tr>
                    <td width=20%><b>Huntersville: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C Hnt:  </b>';

                    while ($chntText = mysql_fetch_array($chntaQuery))
                    {
                        echo $chntText['First'].' '.$chntText['Last'].'<br><br>';
                        $_SESSION['chntd'][$x] = $chntText['First'].' '.$chntText['Last'];
                        $x++;
                    }
                    $x=0;
        echo'              
                    <b>S Hnt:  </b>';
                    while ($shntText = mysql_fetch_array($shntQuery))
                    {
                        echo $shntText['First'].' '.$shntText['Last'].'<br><br>';
                        $_SESSION['shnt'][$x] = $shntText['First'].' '.$shntText['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    <b>Shnt2:  </b>';
                    while ($shnt2Text = mysql_fetch_array($shnt2Query))
                    {
                        echo $shnt2Text['First'].' '.$shnt2Text['Last'];
                        $_SESSION['shnt2'][$x] = $shnt2Text['First'].' '.$shnt2Text['Last'];
                        $x++;
                    }
                    $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="5" cols="80" name="huntnote">Huntersville Notes: </textarea>
                    </td>
                </tr>
            </table>';




////////////////////////////////////////////////////////////////////////////////
//HASC

        $hasca  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'HASC%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $hascaQuery = mysql_query($hasca);

        echo '
            <br><br>
            <table style="background-color:#BBFFBB;">
                <tr>
                    <td width=20%><b>HASC: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        while ($hascText = mysql_fetch_array($hascaQuery))
        {
            echo $hascText['First'].' '.$hascText['Last'].'<br>';
            $_SESSION['hasc'][$x] = $hascText['First'].' '.$hascText['Last'];
            $x++;
        }
        $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="hascnote">HASC Notes: </textarea>
                    </td>
                </tr>
            </table>';




////////////////////////////////////////////////////////////////////////////////
//ESC

        $esca  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'ESC%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $escaQuery = mysql_query($esca);

        echo '
            <br><br>
            <table style="background-color:#4C4CFF;">
                <tr>
                    <td width=20%><b>Edgewater (ESC): </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        while ($escText = mysql_fetch_array($escaQuery))
        {
            echo $escText['First'].' '.$escText['Last'].'<br>';
            $_SESSION['esc'][$x] = $escText['First'].' '.$escText['Last'];
            $x++;
        }
        $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="escnote">ESC Notes: </textarea>
                    </td>
                </tr>
            </table>';



////////////////////////////////////////////////////////////////////////////////
//Ballantyne

        $bala  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'Balnt%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $balaQuery = mysql_query($bala);

        echo '
            <br><br>
            <table style="background-color:#9999FF;">
                <tr>
                    <td width=20%><b>Ballantyne: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        while ($balText = mysql_fetch_array($balaQuery))
        {
            echo $balText['First'].' '.$balText['Last'].'<br>';
            $_SESSION['bal'][$x] = $balText['First'].' '.$balText['Last'];
            $x++;
        }
        $x=0;

        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="balnote">Ballantyne Notes: </textarea>
                    </td>
                </tr>
            </table>';


////////////////////////////////////////////////////////////////////////////////
//SOBA

        $sobaa  = "SELECT First, Last FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE assignment LIKE 'SOBA%' ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $sobaQuery = mysql_query($sobaa);

            echo '
            <br><br>
            <table style="background-color:#EEEEFF;">
                <tr>
                    <td width=20%><b>SOBA: </b></td>
                    <td width=20% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        if (@mysql_num_rows($sobaQuery)==FALSE)
        {
            echo 
                'None';
        }
        else 
        {        
            while ($sobaText = mysql_fetch_array($sobaQuery))
            {
                echo $sobaText['First'].' '.$sobaText['Last'].'<br>';
                $_SESSION['soba'][$x] = $sobaText['First'].' '.$sobaText['Last'];
                $x++;
            }
            $x=0;
        }
        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="sobanote">SOBA Notes: </textarea>
                    </td>
                </tr>
            </table>';    





////////////////////////////////////////////////////////////////////////////////
//Pre- & Post-
        $preposta = "SELECT a.First, a.Last, b.assignment FROM mds AS a ".
                    "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                    "WHERE (b.assignment LIKE '%Pre%' OR b.assignment LIKE '%Post%')".
                    "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                    " AND b.yearnumber=".$datetime->format('Y');
        $prepostQuery = @mysql_query($preposta);

        echo '
            <br><br>
            <table style="background-color:#fad7a0">
                <tr>
                    <td width=20%><b>Pre- & Post-: </b></td>
                    <td width=30% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        if (@mysql_num_rows($prepostQuery)==FALSE)
        {
            echo 
                'None';
        }
        else 
        {        
            while ($prepostText = mysql_fetch_array($prepostQuery))
            {
                echo $prepostText['First'].' '.$prepostText['Last'].', '.$prepostText['assignment'].'<br>';
                $_SESSION['prepost'][$x][0] = $prepostText['First'].' '.$prepostText['Last'];
                $_SESSION['prepost'][$x][1] = $prepostText['assignment'];
                $x++;
            }
            $x=0;
        }
        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="prepostnote">Pre-/Post- Notes: </textarea>
                    </td>
                </tr>
            </table>';





    ////////////////////////////////////////////////////////////////////////////////
    //Unwanted Vacation


        $unwa  =  "SELECT a.First, a.Last, b.assignment FROM mds AS a ".
                  "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                  "WHERE (b.assignment LIKE 'UwVac%' OR b.assignment LIKE 'Unwanted%') ".
                  "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                  " AND b.yearnumber=".$datetime->format('Y');

        $unwQuery = @mysql_query($unwa);

        echo '
            <br><br>
            <table style="background-color:#FFD27F">
                <tr>
                    <td width=20%><b>Unwanted Vacation: </b><br> <font size="2"> 
                    ("UwVac" is preassigned in the schedule,<br>"Unwanted Vac" is assigned <br>by daily need by ORMGR)</font>
                    </td>
                    <td width=30% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        if (@mysql_num_rows($unwQuery)==FALSE)
        {
            echo 
                'None';
        }
        else 
        {        
            while ($unwText = mysql_fetch_array($unwQuery))
            {
                echo $unwText['First'].' '.$unwText['Last'].', '.$unwText['assignment'].'<br>';
                $_SESSION['unw'][$x][0] = $unwText['First'].' '.$unwText['Last'];
                $_SESSION['unw'][$x][1] = $unwText['assignment'];
                $x++;
            }
            $x=0;
        }
        echo'
                    </td>
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="unwnote">Unwanted Vacation Notes: </textarea>
                    </td>
                </tr>
            </table>';


////////////////////////////////////////////////////////////////////////////////
//Call Assignments
        echo '
            <br><br>
            <table style="background-color:#DCDCDC;">
                <tr>
                    <td width=20%><b>Call Assignments: </b></td>
                    <td width=20%><b>Anesthesiologist</b></td>
                    <td width=60%><b>Notes</b></td>
                </tr>';
        

        $corStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'C OR%'".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $corQuery = @mysql_query($corStatement);
        $corText = mysql_fetch_array($corQuery);
        
        $cobStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'C OB%'".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $cobQuery = @mysql_query($cobStatement);
        $cobText = mysql_fetch_array($cobQuery);
        
        $cmatStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'C Mat%' ".
                        "AND assigntype=3 ".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $cmatQuery = @mysql_query($cmatStatement);
        $cmatText = mysql_fetch_array($cmatQuery);
        
        $chntStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'C Hnt%' ".
                        "AND assigntype=3 ".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $chntQuery = @mysql_query($chntStatement);
        $chntText = mysql_fetch_array($chntQuery);
        
        $h1Statement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'H 1%' ".
                        "AND assigntype=3 ".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $h1Query = @mysql_query($h1Statement);
        $h1Text = mysql_fetch_array($h1Query);
        
        $cohStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'C OH%' ".
                        "AND assigntype=3 ".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $cohQuery = @mysql_query($cohStatement);
        $cohText = mysql_fetch_array($cohQuery);
        
        $pedscStatement = "SELECT a.First, a.Last FROM mds AS a ".
                        "INNER JOIN monthassignment AS b ON a.number=b.mdnumber ".
                        "WHERE b.assignment LIKE 'Peds Call%' ".
                        "AND assigntype=3 ".
                        "AND b.daynumber=".$datetime->format('j')." AND b.monthnumber=".$datetime->format('m').
                        " AND b.yearnumber=".$datetime->format('Y');
        $pedscQuery = @mysql_query($pedscStatement);
        $pedcText = mysql_fetch_array($pedscQuery);

        echo'    
                <tr>
                    <td width=20%>C OR</td>
                    <td width=20%>';
        echo $corText['First'].' '.$corText['Last'].'</td>';
        $_SESSION['cor'][$x] = $corText['First'].' '.$corText['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="cornote">C OR Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>C OB</td>
                    <td width=20%>';
        echo $cobText['First'].' '.$cobText['Last'].'</td>';
        $_SESSION['cob'][$x] = $cobText['First'].' '.$cobText['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="cobnote">C OB Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>C Mat</td>
                    <td width=20%>';
        echo $cmatText['First'].' '.$cmatText['Last'].'</td>';
        $_SESSION['cmat'][$x] = $cmatText['First'].' '.$cmatText['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="cmat3note">C Mat Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>C Hnt</td>
                    <td width=20%>';
        echo $chntText['First'].' '.$chntText['Last'].'</td>';
        $_SESSION['chnt'][$x] = $chntText['First'].' '.$chntText['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="chnt3note">C Hnt Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>H 1</td>
                    <td width=20%>';
        echo $h1Text['First'].' '.$h1Text['Last'].'</td>';
        $_SESSION['h1c'][$x] = $h1Text['First'].' '.$h1Text['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="ch1note">H 1 Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>C OH</td>
                    <td width=20%>';
        echo $cohText['First'].' '.$cohText['Last'].'</td>';
        $_SESSION['coh'][$x] = $cohText['First'].' '.$cohText['Last'];
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="cohnote">C OH Notes: </textarea>
                    </td>
                </tr>
                <tr>
                    <td width=20%>Pediatric Call</td>
                    <td width=20%>';
         
        if (@mysql_num_rows($pedscQuery)==FALSE)
        {
            echo $corText['First'].' '.$corText['Last'].' (C OR physician)</td>'; 
        }
        else 
        {        
            echo $pedcText['First'].' '.$pedcText['Last'].'</td>';
            $_SESSION['pedc'][$x] = $pedcText['First'].' '.$pedcText['Last'];
        }
        
        echo'
                    <td width=60% style="padding-top:15px; padding-bottom:15px">
                        <textarea rows="3" cols="80" name="pedcnote">Peds Call Notes: </textarea>
                    </td>
                </tr>
            </table>';
    

////////////////////////////////////////////////////////////////////////////////
//Meetings

        $meetingStatement =  "SELECT * ".
                             "FROM meetings ".
                             "WHERE meetingdate = '".
                             date_format($datetime, "Y-m-d")."'";

        $meetingQuery = @mysql_query($meetingStatement);




        echo'
            <br><br><br>
            <table>
                <tr>
                    <td width=20%><b>Meetings: </b></td>';

                    if (@mysql_num_rows($meetingQuery) == FALSE)
                        echo "<td width=80%>--NO MEETINGS FOR THIS DAY--</td>";
                    else
                    {
                        echo '<td width=80%>
                                <table>
                                    <tr>
                                        <td><b>Anesthesiologist</b></td>
                                        <td><b>Begin Time</b></td>
                                        <td><b>End Time</b></td>
                                        <td><b>Meeting Purpose</b></td>
                                    </tr>';
                        while ($meeting = @mysql_fetch_array($meetingQuery))
                        {
                            $getNameStatement = "SELECT first, last FROM mds WHERE initials LIKE'".$meeting['mdinitials']."'";
                            $getNameQuery = mysql_query($getNameStatement);
                            $NameResult = mysql_fetch_array($getNameQuery);
                            
                            $firstN = $NameResult['first'];
                            $lastN = $NameResult['last'];
                            
                            $btTextStatement = "    SELECT time
                                                    FROM timeperiods
                                                    WHERE timeperiod={$meeting['begintimeperiod']}";
                            $etTextStatement = "    SELECT time
                                                    FROM timeperiods
                                                    WHERE timeperiod={$meeting['endtimeperiod']}";
                            $btTextQuery = mysql_query($btTextStatement);
                            $btText = mysql_fetch_array($btTextQuery);
                            $etTextQuery = mysql_query($etTextStatement);
                            $etText = mysql_fetch_array($etTextQuery);
                            echo '<tr>  
                                    <td>';
                            echo $firstN." ".$lastN;
                            echo '  </td>
                                    <td>'.substr($btText[0],0,5).'</td>
                                    <td>'.substr($etText[0],0,5).'</td>
                                    <td>'.$meeting['commnt'].'</td>';
                            $_SESSION['meeting'][] = $firstN." ".$lastN."/".
                                substr($btText[0],0,5)."/".substr($etText[0],0,5).
                                "/".$meeting['commnt'];
                            echo '</tr>';
                         }
                         echo '</table>
                           </td>';
                     }
        echo'
                </tr>
            </table>
            <br><br><br>

            <table align="center" width="100%" style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px; border:none">
                <tr>
                    <td width="50%" align="center" style="border: none">
                         <input type="submit" name="ORMA" value="SUBMIT" class="btn">
                    </td>
                    </form>
                </tr>
            </table>';
    }
}
?>

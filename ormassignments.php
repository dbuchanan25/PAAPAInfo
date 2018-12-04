<?php
session_start();
    
require_once ('connect2.php');
include ('xmlparse.php');

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

   
    echo'
    <link rel="stylesheet" href="style2.css" type="text/css">
    ';
    
    echo '<title>ORMGR Assignments Page</title>';
    
    /*
    $mdentryStatement = "INSERT INTO mdlog
    VALUES ('{$_SESSION['initials']}','', 'ormpage.php accessed', CURRENT_TIMESTAMP,NULL)";
    mysql_query($mdentryStatement);
     * 
     */

    echo'
        <body><center>';
    
    
    include_once 'menuBar.php';
    menuBar(5663);
    
    
    
    
    echo '
        <br><br>
	<center><zz>OR Manager Assignments</zz><br><br>';
    
    
    $day=32; $number=32;
    if (@$_POST['month'])
    {
        $month = $_POST['month'];
        $_SESSION['m'] = $month;
        $day = $_POST['day'];
        $_SESSION['d'] = $day;
        $year = $_POST['year'];
        $_SESSION['y'] = $year;
        $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }
    
    
    
    if ($day != 32 && $day > $number)
    {
        echo'
            <form method="post" action="ormassignmentspre.php" class="input">
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
            <br><br>';
        
        


        foreach (glob("ormgrassignments_".$datetime->format('Y').$datetime->format('m').$datetime->format('d')."_*.xml") as $filename)
        {
            $myfile = fopen($filename, "r") or die("Unable to open file!");
        }

        if (!isset($myfile))
        {
            
        echo'
            <form method="post" action="ormassignmentspre.php" class="input">
                <table>
                    <tr>
                        <td>NO INFORMATION EXISTS FOR THAT DATE</td>
                    </tr>
                  </table>
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
        $contents=fread($myfile,filesize($filename));
        
        $oanote1 = xmlparse($contents, "<OANOTE>");
        
        echo '
            <h2><b>ASSIGNMENT CHANGES: </b></h2>
            <table style="background-color:#FFFF66;">
                <tr>                    
                    <td width=85% style="text-align:left; padding-left:50px;">'.$oanote1.'
                   </td>
                </tr>
            </table><br><br>';



        echo '
            <table style="background-color:#F5F5C8;">
                <tr>
                    <td width=15%><b>Presbyterian Main: </b></td>
                    <td width=15%><b>Anesthesiologist</b></td>
                    <td width=15%><b>Rooms</b></td>
                    <td width=55%><b>Notes</b></td>
                </tr>
                <tr>
                    <td width=15%>ORMGR</td>
                    <td width=15%>';



        $orm = xmlparse($contents, "<ORMGR>");
        $orm1 = xmlparsem($orm, "<PhysicianName>");
        $orm2 = xmlparse($orm, "<ECT>");
        $orm3 = xmlparse($orm, "<Jutras>");
        $orm4 = xmlparse($orm, "<OrmgrNote>");
        
        $zs = count($orm1);
        
        for ($q1=0; $q1 < $zs-1; $q1++)
        {
            echo $orm1[$q1].'<br>';
        }
        echo $orm1[$zs-1];
        
        echo'                    
                    </td>
                    <td width=15%>
                    ECTs: '.$orm2.', '.            
                    'Jutras: '.$orm3.'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$orm4.'                        
                    </td>
                </tr>';
        echo'
                <tr>
                    <td width=15%>SLate</td>
                    <td width=15%>';

        $sl0 = xmlparse($contents, "<SLATE>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<SLateNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
            echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            echo'
                <tr>
                    <td width=15%>Neuro</td>
                    <td width=15%>';
            
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);

        $sl0 = xmlparse($contents, "<NEURO>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<NeuroNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
            echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            
        echo'
                <tr>
                    <td width=15%>BLong</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);

        $sl0 = xmlparse($contents, "<BLONG>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<BLongNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
            echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
        echo'
                <tr>
                    <td width=15%>S B</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);

        $sl0 = xmlparse($contents, "<SB>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<SBNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
            echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            
        echo'
                <tr>
                    <td width=15%>OB 1</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);

        $sl0 = xmlparse($contents, "<OB1>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl3 = xmlparse($sl0, "<OB1Note>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            
        echo'
                <tr>
                    <td width=15%>SEndo</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s13);

        $sl0 = xmlparse($contents, "<SENDO>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl3 = xmlparse($sl0, "<SEndoNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            
        echo'
                <tr>
                    <td width=15%>SRad</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s13);

        $sl0 = xmlparse($contents, "<SRAD>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl3 = xmlparse($sl0, "<SRadNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
            
        echo'
                <tr>
                    <td width=15%>Pediatric Coverage (Day)</td>
                    <td width=15%>';
        
        unset($s10);
        unset($s11);
        unset($s13);

        $sl0 = xmlparse($contents, "<PED>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<PedNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
        echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>';
        
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);
            
            
        echo'
                <tr>
                    <td width=15%>ERAS</td>
                    <td width=15%>';

        $sl0 = xmlparse($contents, "<ERAS>");
        $sl1 = xmlparsem($sl0, "<PhysicianName>");
        $sl2 = xmlparsem($sl0, "<Room>");
        $sl3 = xmlparse($sl0, "<ERASNote>");

        foreach ($sl1 as $a1)
        {
            echo    
                    $a1.' ';
        }
            echo'
                    </td>
                    <td width=15%>';
        foreach ($sl2 as $a2)
        {
            echo    
                    $a2.' ';
        }
        echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px;">'.$sl3.'
                    </td>
                </tr>
            </table>';
        
        unset($s10);
        unset($s11);
        unset($s12);
        unset($s13);

       
        

        




////////////////////////////////////////////////////////////////////////////////
//CVOR
        
        $aa = xmlparsem($contents, "<H1PhysicianName>");
        $ab = xmlparsem($contents, "<H2PhysicianName>");
        $ac = xmlparse($contents, "<CVORNote>");
        
        echo '
            <br><br>
            <table style="background-color:#C23E33;">
                <tr>
                    <td width=15%><b>CVOR: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px;">
                    <b>H 1:  </b>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        }
                
        echo'   
                    <br><b>H 2:  </b>';
        
        foreach ($ab as $a1)
        {
            echo    
                    $a1.' ';
        }
        
        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ac.'                        
                    </td>
                </tr>
            </table>'; 
        
        unset($aa);
        unset($ab);
        unset($ac);
        





////////////////////////////////////////////////////////////////////////////////
//Orthopedic  
        $aa = xmlparsem($contents, "<COHPhysicianName>");
        $ab = xmlparsem($contents, "<SOHPhysicianName>");
        $ac = xmlparsem($contents, "<SOH2PhysicianName>");
        $ad = xmlparsem($contents, "<BKOHPhysicianName>");
        $ae = xmlparse($contents, "<OrthoNote>");

        echo '
            <br><br>
            <table style="background-color:#50AD39;">
                <tr>
                    <td width=15%><b>Orthopedic: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C OH:  </b>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        }
        
        echo'              
                    <br><b>S OH:  </b>';
        foreach ($ab as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'              
                    <br><b>S OH2:  </b>';
        
        foreach ($ac as $a1)
        {
            echo    
                    $a1.' ';
        }
        
        echo'              
                    <br><b>BK OH:  </b>';
        
        foreach ($ad as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>'; 
        
        unset($aa);
        unset($ab);
        unset($ac);
        unset($ad);
        unset($ae);



////////////////////////////////////////////////////////////////////////////////
//CLTOPS

        $aa = xmlparsem($contents, "<COPS1PhysicianName>");
        $ab = xmlparsem($contents, "<COPS2PhysicianName>");
        $ae = xmlparse($contents, "<OpsNote>");


        echo '
            <br><br>
            <table style="background-color:#E1CCCC;">
                <tr>
                    <td width=15%><b>CLTOPS: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>Ops1:  </b>';

        $za = count($aa);
        
        for ($q=0; $q<$za-1; $q++)
        {
            echo $aa[$q].'<br>';
        }
        echo $aa[$za-1];
        
        echo' 
                    <br><b>Ops2:  </b>';
        
        $zb = count($ab);
        
        for ($q=0; $q<$zb-1; $q++)
        {
            echo $ab[$q].'<br>';
        }
        echo $ab[$zb-1];

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        unset($aa);
        unset($ab);
        unset($ae);


////////////////////////////////////////////////////////////////////////////////
//Midtown

        $aa = xmlparsem($contents, "<MidTnPhysicianName>");
        $ab = xmlparsem($contents, "<MidTn2PhysicianName>");
        $ae = xmlparse($contents, "<MidTnNote>");


        echo '
            <br><br>
            <table style="background-color:#C46d66;">
                <tr>
                    <td width=15%><b>Midtown: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>MidTn:  </b>';
        
        $zc = count($aa);
        
        for ($q=0; $q<$zc-1; $q++)
        {
            echo $aa[$q].'<br>';
        }
        echo $aa[$zc-1];
        
        echo'              
                    <br><b>MidTn2:  </b>';
        
       $zd = count($ab);
        
        for ($q=0; $q<$zd-1; $q++)
        {
            echo $ab[$q].'<br>';
        }
        echo $ab[$zd-1];

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($ab);
        unset($ae);



////////////////////////////////////////////////////////////////////////////////
//Southpark

        $aa = xmlparsem($contents, "<SpkPhysicianName>");
        $ab = xmlparsem($contents, "<Spk2PhysicianName>");
        $ae = xmlparse($contents, "<SpkNote>");


         echo '
            <br><br>
            <table style="background-color:#507abf;">
                <tr>
                    <td width=15%><b>Southpark: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>Spk:  </b>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'              
                    <br><b>Spk2:  </b>';
        
        foreach ($ab as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($ab);
        unset($ae);


        ////////////////////////////////////////////////////////////////////////////
        //MATTHEWS

        $aa = xmlparsem($contents, "<CMatPhysicianName>");
        $ab = xmlparsem($contents, "<SMLatePhysicianName>");
        $ac = xmlparsem($contents, "<SMatPhysicianName>");
        $ae = xmlparse($contents, "<MatthewsNote>");


        echo '
            <br><br>
            <table style="background-color:#DDFFFF;">
                <tr>
                    <td width=15%><b>Matthews: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C Mat:  </b>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'              
                    <br><b>SMLate:  </b>';
        
        foreach ($ab as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    <br><b>S Mat:  </b>';
        
        foreach ($ac as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        unset($aa);
        unset($ab);
        unset($ac);
        unset($ae);


////////////////////////////////////////////////////////////////////////////////
//MASC

        $az = xmlparse($contents, "<MASC>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ae = xmlparse($az, "<MASCNote>");

        echo '
            <br><br>
            <table style="background-color:#EEFFFF;">
                <tr>
                    <td width=15%><b>MASC: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($az);
        unset($aa);
        unset($ae);



////////////////////////////////////////////////////////////////////////////////
//Huntersville


        $aa = xmlparsem($contents, "<CHntPhysicianName>");
        $ab = xmlparsem($contents, "<SHntPhysicianName>");
        $ac = xmlparsem($contents, "<Shnt2PhysicianName>");
        $ae = xmlparse($contents, "<HuntersvilleNote>");


        echo '
            <br><br>
            <table style="background-color:#DDFFFF;">
                <tr>
                    <td width=15%><b>Huntersville: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">
                    <b>C Hnt:  </b>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'              
                    <br><b>S Hnt:  </b>';
        
        foreach ($ab as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    <br><b>Shnt2:  </b>';
        
        foreach ($ac as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        unset($aa);
        unset($ab);
        unset($ac);
        unset($ae);


////////////////////////////////////////////////////////////////////////////////
//HASC

        $az = xmlparse($contents, "<HASC>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ae = xmlparse($az, "<HASCNote>");

        echo '
            <br><br>
            <table style="background-color:#EEFFFF;">
                <tr>
                    <td width=15%><b>HASC: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($az);
        unset($ae);



////////////////////////////////////////////////////////////////////////////////
//ESC

        $az = xmlparse($contents, "<ESC>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ae = xmlparse($az, "<ESCNote>");

        echo '
            <br><br>
            <table style="background-color:#4C4CFF;">
                <tr>
                    <td width=15%><b>Edgewater (ESC): </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };

        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($az);
        unset($ae);



////////////////////////////////////////////////////////////////////////////////
//Ballantyne

        $az = xmlparse($contents, "<BALLANTYNE>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ae = xmlparse($az, "<BalNote>");

        echo '
            <br><br>
            <table style="background-color:#9999FF;">
                <tr>
                    <td width=15%><b>Ballantyne: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };


        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($az);
        unset($ae);


////////////////////////////////////////////////////////////////////////////////
//SOBA

        $az = xmlparse($contents, "<SOBA>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ae = xmlparse($az, "<SOBANote>");

            echo '
            <br><br>
            <table style="background-color:#EEEEFF;">
                <tr>
                    <td width=15%><b>SOBA: </b></td>
                    <td width=15% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'
                    </td>
                    <td width=70% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($az);
        unset($ae);




////////////////////////////////////////////////////////////////////////////////
//Pre- & Post-
        $az = xmlparse($contents, "<PREPOST>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ab = xmlparsem($az, "<Assignment>");
        $ae = xmlparse($az, "<PrePostNote>");

        echo '
            <br><br>
            <table style="background-color:#fad7a0">
                <tr>
                    <td width=15%><b>Pre- & Post-: </b></td>
                    <td width=30% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        
        
        $pieces = count($aa);
        
        for ($q=0; $q<$pieces; $q++)
        {
            echo $aa[$q].' - ';
            echo $ab[$q].'<br> ';
        }
 

        echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($ab);
        unset($ae);
        unset($az);
        
        
////////////////////////////////////////////////////////////////////////////////
//Unwanted Vacation

        $az = xmlparse($contents, "<UNWANTED>");
        $aa = xmlparsem($az, "<PhysicianName>");
        $ab = xmlparsem($az, "<Assignment>");
        $ae = xmlparse($az, "<UnwNote>");
        
        

        echo '
            <br><br>
            <table style="background-color:#FFD27F">
                <tr>
                    <td width=15%><b>Unwanted Vacation: </b><br> <font size="2"> 
                    ("UwVac" is preassigned in the schedule,<br>"Unwanted Vac" is assigned <br>by daily need by the ORMGR)</font>
                    </td>
                    <td width=30% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">';
        
        foreach ($aa as $a1)
        {
            if ($a1 === "" || $a1 === " ")
            {}
            else
            {
                echo    
                        $a1.' - ';
                foreach ($ab as $a2)
                {
                    if ($a2 === "" || $a2 === " ")
                    {}
                    else
                    {
                         echo $a2.'<br> ';
                    }
                }
                   
            }
        };

        echo'
                    </td>
                    <td width=55% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($ab);
        unset($ae);
        unset($az);


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
        

        $az = xmlparse($contents, "<CALLS>");
        
        
        $ay = xmlparse($az, "<COR>");        
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");

        echo'    
                <tr>
                    <td width=20%>C OR</td>
                    <td width=20%>';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        
        unset($aa);
        unset($ay);
        unset($ae);
        
        
        
        $ay = xmlparse($az, "<COB>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        
        echo'
                <tr>
                    <td width=20%>C OB</td>
                    <td width=20%>';

        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        
        unset($aa);
        unset($ay);
        unset($ae);
        
        
        
        
        $ay = xmlparse($az, "<CMat>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        
        echo'
                <tr>
                    <td width=20%>C Mat</td>
                    <td width=20%>';
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        
        unset($aa);
        unset($ay);
        unset($ae);
        
        
        
        
        $ay = xmlparse($az, "<CHnt>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        
        echo'
                <tr>
                    <td width=20%>C Hnt</td>
                    <td width=20%>';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        
        unset($aa);
        unset($ay);
        unset($ae);       
        
        
        
        
        
        $ay = xmlparse($az, "<H1>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        echo'
                <tr>
                    <td width=20%>H 1</td>
                    <td width=20%>';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        
        unset($aa);
        unset($ay);
        unset($ae);
        
        
        $ay = xmlparse($az, "<COH>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        
        echo'
                <tr>
                    <td width=20%>C OH</td>
                    <td width=20%>';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>';
        unset($aa);
        unset($ay);
        unset($ae);
        
        
        $ay = xmlparse($az, "<PedsCall>");  
        $aa = xmlparsem($ay, "<PhysicianName>");
        $ae = xmlparse($ay, "<Note>");
        
        echo'
                <tr>
                    <td width=20%>Pediatric Call</td>
                    <td width=20%>';
        
        foreach ($aa as $a1)
        {
            echo    
                    $a1.' ';
        };
        
        echo'
                    </td>
                    <td width=60% style="text-align:left; padding-left:50px; padding-top:15px; padding-bottom:15px">'.$ae.'
                    </td>
                </tr>
            </table>';
        
        unset($aa);
        unset($ay);
        unset($ae);
    

        
        
////////////////////////////////////////////////////////////////////////////////
//Meetings

        $ay = xmlparse($contents, "<MEETINGS>");

        if (strlen($ay)>0)
        {
            $aa = xmlparsem($ay, "<Meeting>");
           


            echo'
                <br><br><br>
                <h2><b>Meetings: </b></h2>
                <table style="background-color:#BBBBBB;">
                    <tr>
                        <td><b>Anesthesiologist</b></td>
                        <td><b>Begin Time</b></td>
                        <td><b>End Time</b></td>
                        <td><b>Meeting Purpose</b></td>
                    </tr>';
           
            foreach ($aa as $a1)
            {
                $myArray = explode('/', $a1);

                echo '<tr>  
                                        <td>';
                                echo $myArray[0];
                                echo '  </td>
                                        <td>'.$myArray[1].'</td>
                                        <td>'.$myArray[2].'</td>
                                        <td>'.$myArray[3].'</td>';
                                echo '</tr>';
            }

            echo'
                </table>
                <br><br><br>';
        }
        else
        {
            echo '<br><br><br>
                <h2><b>Meetings: </b></h2>
                <table style="background-color:#BBBBBB;">
                    <tr>
                        <td>No meetings for this date.</td>
                    </tr>
                </table>';
        }
    }
    }
}
?>

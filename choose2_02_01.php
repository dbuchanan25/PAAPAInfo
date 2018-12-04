<?php
$a = session_id();
if(empty($a)) session_start();

$page_title = "Patient Satisfaction Results";


require_once ($_SESSION['login2string']);
include ('calldisplay.php');
include ('includes/header.php');

/*
 * Version 03_01
 */
/*
 * Last Revised 2014-07-22 to include only DOS for parameter because of 
 * privacy concerns.
 * Revised 2011-05-01
 */

$mdentryStatement = "INSERT INTO mdlog
VALUES ('{$_SESSION['initials']}', '', 'choose2_02_01.php accessed - patient satisfaction results', CURRENT_TIMESTAMP,NULL)";
mysql_query($mdentryStatement);


	 if( !ini_get('safe_mode') )
	 { 
            set_time_limit(240); 
         }

	 $tp = $_POST['timeperiod'];
	 if ($tp != "all")
	 {
	    $today = getdate(date("U"));
	    if ($tp == "three")
            {
               $startdate = $today[0]-7776000;
               $str = " AND answers.srvdate > ".date("Ymd", $startdate);
            }
            else if ($tp == "six")
            {
               $startdate = $today[0]-7776000*2;
               $str = " AND answers.srvdate > ".date("Ymd", $startdate);
            }
            else if ($tp == "nine")
            {
               $startdate = $today[0]-7776000*3;
               $str = " AND answers.srvdate > ".date("Ymd", $startdate);
            } 
            else if ($tp == "twelve")
            {
               $startdate = $today[0]-7776000*4;
               $str = " AND answers.srvdate > ".date("Ymd", $startdate);
            }
            else if ($tp == "year")
            {
               $newyears = mktime(0,0,0,1,1,date("Y"));
               $str = " AND answers.srvdate > ".date("Ymd",$newyears);
            }
            else if ($tp == "lastq")
            {
                $currentMonth = $today['month'];
                $currentYear = $today['year'];
                if ($currentMonth < 4)
                {
                    $lastquarterstart = mktime(0,0,0,9,30,$currentYear-1);
                    $lastquarterend = mktime(0,0,0,1,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth>3 && $currentMonth<7)
                {
                    $lastquarterstart = mktime(0,0,0,12,31,$currentYear-1);
                    $lastquarterend = mktime(0,0,0,4,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth>6 && $currentMonth<10)
                {
                    $lastquarterstart = mktime(0,0,0,3,31,$currentYear);
                    $lastquarterend = mktime(0,0,0,7,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth>9)
                {
                    $lastquarterstart = mktime(0,0,0,6,30,$currentYear);
                    $lastquarterend = mktime(0,0,0,10,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
            }
            else if ($tp == "twoq")
            {
                $currentMonth = $today['month'];
                $currentYear = $today['year'];
                if ($currentMonth < 7 && $currentMonth > 3)
                {
                    $lastquarterstart = mktime(0,0,0,9,30,$currentYear-1);
                    $lastquarterend = mktime(0,0,0,1,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth>6 && $currentMonth<10)
                {
                    $lastquarterstart = mktime(0,0,0,12,31,$currentYear-1);
                    $lastquarterend = mktime(0,0,0,4,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth>9)
                {
                    $lastquarterstart = mktime(0,0,0,3,31,$currentYear);
                    $lastquarterend = mktime(0,0,0,7,1,$currentYear);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
                else if ($currentMonth < 4)
                {
                    $lastquarterstart = mktime(0,0,0,6,30,$currentYear-1);
                    $lastquarterend = mktime(0,0,0,10,1,$currentYear-1);
                    $str = " AND answers.srvdate > ".date("Ymd",$lastquarterstart).
                        " AND answers.srvdate < ".date("Ymd",$lastquarterend);
                }
            }
	 }
	 else
	 {
	    $str ="";
         }
	 
	 //END OF STRING CONSTRUCTION FOR PARSING THE DATABASE
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 //2011-04-30
	 /*
	 IN THE NEW MODEL OF THE DATABASE WE NEED TO DO AN INNER JOIN WITH "answerdemographicsws" 
	 TO GET THE TYPE OF ANESTHESIA, AGE, LOCATION
	 THIS INFORMATION WILL NO LONGER BE LOCATED IN "answers" ALONE
	 */
	 
	 
	 
	 
	 	
		 

	 if ($_SESSION['initials']=='DB' || $_SESSION['initials']=='JD')
	 {
             echo '<center><h2>Patient Satisfaction Survey Data</center></h2>
             <center><h2>For: '.$_POST['dbinitials'].'</center></h2><br>';
	 }
	 else
	 {
             echo '<center><h2>Patient Satisfaction Survey Data</center></h2>
             <center><h2>For: '.$_SESSION['initials'].'</center></h2><br>';
	 }
	 
	 $n = array();
	 
	 
	 
	 
	 /////////////////////////////////////////////
	 //GET COUNTS FOR ALL THE QUESTIONS ANSWERED//
	 /////////////////////////////////////////////
	 /////////////////////////////////////////////////////////////////////////////////////////////
	 //$my_t=getdate(date("U"));
     //echo 'Line 426: '.$my_t[seconds].' / '. $my_t[minutes];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    WHERE answers.q1>0 
                    AND answers.q1<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q2>0 
                    AND answers.q2<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q3>0 
                    AND answers.q3<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q4>0 
                    AND answers.q4<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q5>0 
                    AND answers.q5<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q6>0 
                    AND answers.q6<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q7>0 
                    AND answers.q7<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q8>0 
                    AND answers.q8<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q9>0 
                    AND answers.q9<3 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 
	 
	 
	 ////////////////////////////////////////////////
	 //GET SUMS FOR ALL THE QUESTIONS FOR THE GROUP//
	 ////////////////////////////////////////////////
	 /////////////////////////////////////////////////////////////////////////////////////////////
	 //$my_t=getdate(date("U"));
     //echo 'Line 499: '.$my_t[seconds].' / '. $my_t[minutes]; 
	 
	 
	 $sum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers
                    
                    
                    WHERE answers.q1>0 
                    AND answers.q1<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum1 += $groupa[0];
	 
	 
	 $sum2 = 0;
	 $groupq = "SELECT q2
	            FROM answers
                    
                    
                    WHERE answers.q2>0 
                    AND answers.q2<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum2 += $groupa[0];	
	 
	 $sum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers
                    
                    
                    WHERE answers.q3>0 
                    AND answers.q3<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum3 += $groupa[0];	
	 
	 $sum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers
                    
                    
                    WHERE answers.q4>0 
                    AND answers.q4<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum4 += $groupa[0];	  
	 
         $sum5 = 0;
	 $groupq = "SELECT q5 
	            FROM answers
                    
                    
                    WHERE answers.q5>0 
                    AND answers.q5<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum5 += $groupa[0];	
	 
         $sum6 = 0;
	 $groupq = "SELECT q6 
	            FROM answers
                    
                    
                    WHERE answers.q6>0 
                    AND answers.q6<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum6 += $groupa[0];		
	 
         $sum7 = 0;
	 $groupq = "SELECT q7 
	            FROM answers
                    
                    
                    WHERE answers.q7>0 
                    AND answers.q7<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum7 += $groupa[0];	 
	 
         $sum8 = 0;
	 $groupq = "SELECT q8 
	            FROM answers
                    
                    
                    WHERE answers.q8>0 
                    AND answers.q8<6 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum8 += $groupa[0];	 
	 
         $sum9 = 0;
	 $groupq = "SELECT q9 
	            FROM answers
                    
                    
                    WHERE answers.q9>0 
                    AND answers.q9<3 
                    AND answers.questionset=1".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum9 += $groupa[0];   
		
		
		
		   
	 /////////////////////////////////////////////////
	 //CALCULATE THE MEANS FOR EACH OF THE QUESTIONS//
	 /////////////////////////////////////////////////
	 /////////////////////////////////////////////////////////////////////////////////////////////
	 //$my_t=getdate(date("U"));
     //echo 'Line 578: '.$my_t[seconds].' / '. $my_t[minutes]; 
	 
		   
	 $meangroup = array();
	 $x = 0;
     @$meangroup[] = $sum1/$n[$x];
	 $x++;
     @$meangroup[] = $sum2/$n[$x];
	 $x++;
     @$meangroup[] = $sum3/$n[$x];
	 $x++;
     @$meangroup[] = $sum4/$n[$x];
	 $x++;
     @$meangroup[] = $sum5/$n[$x];
	 $x++;
     @$meangroup[] = $sum6/$n[$x];
	 $x++;
     @$meangroup[] = $sum7/$n[$x];
	 $x++;
     @$meangroup[] = $sum8/$n[$x];
	 $x++;
     @$meangroup[] = (2-($sum9/$n[$x]))*$n[$x];
	 $x++;

	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 ///////////////////////////////////
	 //GET THE ANESTHESIOLOGIST'S NAME//
	 ///////////////////////////////////
	 
	 if ($_SESSION['initials']==='DB' || $_SESSION['initials']==='JD')
	 {
             $lastnamemdq = "SELECT last
                             FROM mdps
                             WHERE initials='{$_POST['dbinitials']}'";
             $lastnamer = mysql_query($lastnamemdq);
             $lastnamem = mysql_fetch_row($lastnamer);
             $lastname = trim($lastnamem[0]);
	 }
	 else
	 {
             $lastnamemdq = "SELECT last
                             FROM mdps
                             WHERE initials='{$_SESSION['initials']}'";
             $lastnamer = mysql_query($lastnamemdq);
             $lastnamem = mysql_fetch_row($lastnamer);
             $lastname = trim($lastnamem[0]);
	 }
    //////////////////////////////////////////////////////////////////////////////////////////////
	//$my_t=getdate(date("U"));
    //echo 'Line 629: '.$my_t[seconds].' / '. $my_t[minutes]; 
	 
	 
	 
	 
	 
	 

	 
	 /////////////////////////////////////////////////////////////////////
	 //GET THE PATIENT ID'S THAT A PARTICULAR ANESTHESIOLOGIST CARED FOR//
	 /////////////////////////////////////////////////////////////////////
	 $mdn = array();
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q1>0
                    AND answers.q1<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q2>0
                    AND answers.q2<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q3>0
                    AND answers.q3<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q4>0
                    AND answers.q4<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q5>0
                    AND answers.q5<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q6>0
                    AND answers.q6<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q8>0
                    AND answers.q8<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];
	 
	 $countq1 = $mdn[0];
	 $countq2 = $mdn[1];
	 $countq3 = $mdn[2];
	 $countq4 = $mdn[3];
	 $countq5 = $mdn[4];
	 $countq6 = $mdn[5];
	 $countq7 = $mdn[6];
	 $countq8 = $mdn[7];
	 $countq9 = $mdn[8];
	 
	 
	 
	 $mdqsum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers 
                    
                    
                    WHERE answers.q1>0
                    AND answers.q1<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum1 += $groupa[0];
	 
	 $mdqsum2 = 0;
	 $groupq = "SELECT q2 
	            FROM answers 
                    
                    
                    WHERE answers.q2>0
                    AND answers.q2<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum2 += $groupa[0];	
	 
	 $mdqsum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers 
                    
                    
                    WHERE answers.q3>0
                    AND answers.q3<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum3 += $groupa[0];	
	 
	 $mdqsum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers 
                    
                    
                    WHERE answers.q4>0
                    AND answers.q4<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum4 += $groupa[0];	  
	 
	 $mdqsum5 = 0;
	 $groupq = "SELECT q5 
	            FROM answers 
                    
                    
                    WHERE answers.q5>0
                    AND answers.q5<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum5 += $groupa[0];	
	 
	 $mdqsum6 = 0;
	 $groupq = "SELECT q6 
	            FROM answers 
                    
                    
                    WHERE answers.q6>0
                    AND answers.q6<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum6 += $groupa[0];		
	 
	 $mdqsum7 = 0;
	 $groupq = "SELECT q7 
	            FROM answers 
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum7 += $groupa[0];	 
	 
	 $mdqsum8 = 0;
	 $groupq = "SELECT q8 
	            FROM answers 
                    
                    
                    WHERE answers.q8>0
                    AND answers.q8<6
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum8 += $groupa[0];	 
	 
	 $mdqsum9 = 0;
	 $groupq = "SELECT q9 
	            FROM answers 
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=1
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum9 += $groupa[0];


	 $mdmean = array();
	 @$mdmean[] = $mdqsum1/$countq1;
	 @$mdmean[] = $mdqsum2/$countq2;
	 @$mdmean[] = $mdqsum3/$countq3;
	 @$mdmean[] = $mdqsum4/$countq4;
	 @$mdmean[] = $mdqsum5/$countq5;
	 @$mdmean[] = $mdqsum6/$countq6;
	 @$mdmean[] = $mdqsum7/$countq7;
	 @$mdmean[] = $mdqsum8/$countq8;
	 @$mdmean[] = (2-($mdqsum9/$countq9))*$countq9;
	 
	 $countq = array($countq1, $countq2, $countq3, $countq4, $countq5, 
	                 $countq6, $countq7, $countq8, $countq9);

	 
	 
	 echo '
	 	   <center><h3>Anesthesia Responses</h3></center><br>';
	 calldisplay($meangroup, $mdmean, $n, $countq, 1);
	 
	 
	 
	 


	 
	 ////////////////////////////////////////////////////
	 //GET ALL THE INFORMATION FOR THE OB EPIDURAL DATA//
	 ////////////////////////////////////////////////////
	 
	 unset($n);
	 unset ($mdn);
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                     
                    WHERE answers.q1>0 
                    AND answers.q1<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q2>0 
                    AND answers.q2<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q3>0 
                    AND answers.q3<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q4>0 
                    AND answers.q4<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q5>0 
                    AND answers.q5<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                     
                    
                    WHERE answers.q6>0 
                    AND answers.q6<6 
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $n[] = $groupa[0]; 
	 
	 
	 
	 $sum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers 
                    
                    
                    WHERE answers.q1>0
                    AND answers.q1<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum1 += $groupa[0];
	 
	 $sum2 = 0;
	 $groupq = "SELECT q2 
	            FROM answers 
                    
                    
                    WHERE answers.q2>0
                    AND answers.q2<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum2 += $groupa[0];	
	 
	 $sum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers 
                    
                    
                    WHERE answers.q3>0
                    AND answers.q3<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum3 += $groupa[0];	
	 
	 $sum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers 
                    
                    
                    WHERE answers.q4>0
                    AND answers.q4<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum4 += $groupa[0];	  
	 
	 $sum5 = 0;
	 $groupq = "SELECT q5 
	            FROM answers 
                    
                    
                    WHERE answers.q5>0
                    AND answers.q5<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum5 += $groupa[0];	
	 
	 $sum6 = 0;
	 $groupq = "SELECT q6 
	            FROM answers 
                    
                    
                    WHERE answers.q6>0
                    AND answers.q6<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum6 += $groupa[0];		
	 
	 $sum7 = 0;
	 $groupq = "SELECT q7 
	            FROM answers 
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum7 += $groupa[0];	 
	 
	 $sum8 = 0;
	 $groupq = "SELECT q9 
	            FROM answers 
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=2".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $sum8 += $groupa[0];	 
	 
		   
	 $meangroup2 = array();
	 $x=0;
	 @$meangroup2[] = $sum1/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum2/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum3/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum4/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum5/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum6/$n[$x];
	 $x++;
	 @$meangroup2[] = $sum7/$n[$x];
	 $x++;
	 @$meangroup2[] = (2-($sum8/$n[$x]))*$n[$x];
	 $x++;
	 
	 


		   
         $mdqsum1 = 0;
	 $mdqsum2 = 0;
	 $mdqsum3 = 0;
	 $mdqsum4 = 0;
	 $mdqsum5 = 0;
	 $mdqsum6 = 0;
	 $mdqsum7 = 0;
	 $mdqsum8 = 0;
	 $mdqsum9 = 0;
	 $countq1 = 0;
	 $countq2 = 0;
	 $countq3 = 0;
	 $countq4 = 0;
	 $countq5 = 0;
	 $countq6 = 0;
	 $countq7 = 0;
	 $countq8 = 0;
	 
	 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q1>0
                    AND answers.q1<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q2>0
                    AND answers.q2<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q3>0
                    AND answers.q3<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q4>0
                    AND answers.q4<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];  
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q5>0
                    AND answers.q5<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q6>0
                    AND answers.q6<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0];	
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 $groupq = "SELECT count(*) 
	            FROM answers 
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 @$groupa = mysql_fetch_row ($group);
	 $mdn[] = $groupa[0]; 
	 
	 
	 
	 $countq1 = $mdn[0];
	 $countq2 = $mdn[1];
	 $countq3 = $mdn[2];
	 $countq4 = $mdn[3];
	 $countq5 = $mdn[4];
	 $countq6 = $mdn[5];
	 $countq7 = $mdn[6];
	 $countq8 = $mdn[7];
	 
	 
	 $mdqsum1 = 0;
	 $groupq = "SELECT q1 
	            FROM answers 
                    
                    
                    WHERE answers.q1>0
                    AND answers.q1<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum1 += $groupa[0];
	 
	 $mdqsum2 = 0;
	 $groupq = "SELECT q2 
	            FROM answers 
                    
                    
                    WHERE answers.q2>0
                    AND answers.q2<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum2 += $groupa[0];	
	 
	 $mdqsum3 = 0;
	 $groupq = "SELECT q3 
	            FROM answers 
                    
                    
                    WHERE answers.q3>0
                    AND answers.q3<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum3 += $groupa[0];	
	 
	 $mdqsum4 = 0;
	 $groupq = "SELECT q4 
	            FROM answers
                    
                    
                    WHERE answers.q4>0
                    AND answers.q4<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum4 += $groupa[0];	  
	 
	 $mdqsum5 = 0;
	 $groupq = "SELECT q5 
	            FROM answers
                    
                    
                    WHERE answers.q5>0
                    AND answers.q5<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum5 += $groupa[0];	
	 
	 $mdqsum6 = 0;
	 $groupq = "SELECT q6 
	            FROM answers
                    
                    
                    WHERE answers.q6>0
                    AND answers.q6<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum6 += $groupa[0];		
	 
	 $mdqsum7 = 0;
	 $groupq = "SELECT q7 
	            FROM answers
                    
                    
                    WHERE answers.q7>0
                    AND answers.q7<6
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum7 += $groupa[0];	 
	 
	 $mdqsum8 = 0;
	 $groupq = "SELECT q9
	            FROM answers
                    
                    
                    WHERE answers.q9>0
                    AND answers.q9<3
                    AND answers.questionset=2
                    AND answers.anesthesiologist like '$lastname'".$str;
	 $group = mysql_query($groupq);
	 while (@$groupa = mysql_fetch_row($group))
	 	   $mdqsum8 += $groupa[0];	 
	 
	 
	 
	 $mdmean2 = array();
	 @$mdmean2[] = $mdqsum1/$countq1;
	 @$mdmean2[] = $mdqsum2/$countq2;
	 @$mdmean2[] = $mdqsum3/$countq3;
	 @$mdmean2[] = $mdqsum4/$countq4;
	 @$mdmean2[] = $mdqsum5/$countq5;
	 @$mdmean2[] = $mdqsum6/$countq6;
	 @$mdmean2[] = $mdqsum7/$countq7;
	 @$mdmean2[] = (2-($mdqsum8/$countq8))*$countq8;
	 
	 unset ($countq);
        $countq = array($countq1, $countq2, $countq3, $countq4,
	                 $countq5, $countq6, $countq7, $countq8);
	 
	 echo '
	 	   <br><center><h3>Labor Epidural Responses</h3></center><br>';
	 calldisplay($meangroup2, $mdmean2, $n, $countq, 2);

	 
	echo ' <br><br>
		   <link rel="stylesheet" href="stylepatientsatisfaction.css" type="text/css">
		   <table align="center" width="100%">
		         <form method="post" action="psparams_02_01.php" class="input">
		         <tr>
		         <td width="50%" align="center">
				 <input type="submit" name="PAMS" value="PAMS" class="btn">
				 </td>
				 <td width="50%" align="center">
				 <input type="submit" name="PAMS" value="Patient Satisfaction Survey" class="btn"> 
				 </form>
		         </td>
		         </tr>
		         </table>';	   
		   echo '<br><br><br><br>';
	
?>
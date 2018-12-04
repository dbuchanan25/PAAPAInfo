<?php
function isweekend ($datet)
{
//////////////////////////////////////////////////////////////////////////////////////////////////
//If the day is a weekday $_SESSION['dtb']=0, if a weekend $_SESSION['dtb']=1                   //
//($_SESSION['dty'], $_SESSION['dtm'], $_SESSION['dai'])                                        //
//////////////////////////////////////////////////////////////////////////////////////////////////
   $dtb = $datet->format('N');
   
   if ($dtb>5)
      return 1;
   
   //Check for Christmas
   else if (
   		   ($_SESSION['dtm']==12 && $_SESSION['dai']==25)
           )
      return 1;
   
   //Check for New Year's
   else if (
                   ($_SESSION['dtm']==1 && $_SESSION['dai']==1)
           )
      return 1;
   
   //Check for Independence Day
   else if (
                    
                   ($_SESSION['dtm']==7 && $_SESSION['dai']==4) ||
                   ($_SESSION['dtm']==7 && $_SESSION['dai']==3 && $_SESSION['dty']==2015)
           )
      return 1;
   
   //Check for Memorial Day
   else if (
                   ($_SESSION['dtm']==5 && $_SESSION['dai']==27 && $_SESSION['dty']==2013)
		   ||
		   ($_SESSION['dtm']==5 && $_SESSION['dai']==26 && $_SESSION['dty']==2014)
		   ||
		   ($_SESSION['dtm']==5 && $_SESSION['dai']==25 && $_SESSION['dty']==2015)
                   ||
		   ($_SESSION['dtm']==5 && $_SESSION['dai']==30 && $_SESSION['dty']==2016)
                   ||
		   ($_SESSION['dtm']==5 && $_SESSION['dai']==29 && $_SESSION['dty']==2017)
		   )
      return 1;
   
   //Check for Thanksgiving Day
   else if (
                   ($_SESSION['dtm']==11 && $_SESSION['dai']==22 && $_SESSION['dty']==2012)
		   ||
		   ($_SESSION['dtm']==11 && $_SESSION['dai']==28 && $_SESSION['dty']==2013)
		   ||
		   ($_SESSION['dtm']==11 && $_SESSION['dai']==27 && $_SESSION['dty']==2014)
                   ||
		   ($_SESSION['dtm']==11 && $_SESSION['dai']==26 && $_SESSION['dty']==2015)
                   ||
		   ($_SESSION['dtm']==11 && $_SESSION['dai']==24 && $_SESSION['dty']==2016)
                   ||
		   ($_SESSION['dtm']==11 && $_SESSION['dai']==23 && $_SESSION['dty']==2017)
		   )
      return 1;
   
   //Check for Labor Day
   else if (
                   ($_SESSION['dtm']==9 && $_SESSION['dai']==2 && $_SESSION['dty']==2013)
		   ||
		   ($_SESSION['dtm']==9 && $_SESSION['dai']==1 && $_SESSION['dty']==2014)
		   ||
		   ($_SESSION['dtm']==9 && $_SESSION['dai']==7 && $_SESSION['dty']==2015)
                   ||
		   ($_SESSION['dtm']==9 && $_SESSION['dai']==5 && $_SESSION['dty']==2016)
                   ||
		   ($_SESSION['dtm']==9 && $_SESSION['dai']==4 && $_SESSION['dty']==2017)
		   )
      return 1;
   
   else 
      return 0;
}

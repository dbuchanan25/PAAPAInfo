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


   
    echo'
    <link rel="stylesheet" href="style2.css" type="text/css">
    ';
    
    $datetime = new DateTime('now');
    

    foreach (glob("ormgrassignments_".$_SESSION['y'].$_SESSION['m'].$_SESSION['d']."_*.xml") as $filename)
    {
        rename($filename, "backup".$filename);
    }


    $myfile = fopen("ormgrassignments_".$_SESSION['y'].$_SESSION['m'].$_SESSION['d']."_".
            $datetime->format('YmdHis').".xml", "w");
    
    $xmlstring = 
"<?xml version=\"1.0\" encoding=\"utf-8\"?>
<AnesthesiaRecords xmlns=\"http://www.paapa.us//PAAPAInfo//ormassignment.xsd\">\n
<RecordHeader>
    <CreatedBy>".$_SESSION["initials"]."</CreatedBy>
    <CreateDate>".$datetime->format('YmdHis')."</CreateDate>
    <AssignmentDate>".$_SESSION['y'].$_SESSION['m'].$_SESSION['d']."</AssignmentDate>
</RecordHeader>\n
<Assignments>\n\n";
    
    
    
//OverAll Note
$xmlstring .= "    <OANOTE>\n";
$xmlstring = $xmlstring."        ".$_POST["oanote"]."\n";
$xmlstring .= "    </OANOTE>\n";
    
    
    
    
//ORMGR
if (isset ($_SESSION['ormgr']))
{
    $xmlstring = $xmlstring.
    "    <ORMGR>\n";

    foreach($_SESSION["ormgr"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }

    $xmlstring = $xmlstring.

    "        <ECT>".$_POST["ect"]."</ECT>
        <Jutras>";
    
    $jtc = count($_POST['jutras']);
    
    for ($zw=0; $zw<$jtc-1; $zw++)
    {
        $xmlstring.= $_POST['jutras'][$zw].", ";
    }
    if (isset($_POST['jutras'][$jtc-1]))
    {
        $xmlstring.= $_POST['jutras'][$jtc-1];
    }
    $xmlstring.= "</Jutras>\n";

    if ($_POST["ormgrnote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <OrmgrNote>".$_POST["ormgrnote"]."</OrmgrNote>\n";
    }

    $xmlstring = $xmlstring.
    "    </ORMGR>\n\n"; 
}





//SLATE
if (isset($_SESSION['slate']))
{
    $xmlstring = $xmlstring.
    "    <SLATE>\n";

    foreach($_SESSION["slate"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    foreach ($_POST["slate"] as $room)
    {
        $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
    }
    if ($_POST["slatenote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <SLateNote>".$_POST["slatenote"]."</SLateNote>\n";
    }

    $xmlstring = $xmlstring.
    "    </SLATE>\n\n"; 
}


//NEURO
if (isset($_SESSION['neuro']))
{
    $xmlstring = $xmlstring.
    "    <NEURO>\n";  
    foreach($_SESSION["neuro"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    foreach ($_POST["neuro"] as $room)
    {
        $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
    }
    if ($_POST["neuronote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <NeuroNote>".$_POST["neuronote"]."</NeuroNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </NEURO>\n\n";
}


//BLONG
if (isset($_SESSION['blong']))
{
    $xmlstring = $xmlstring.
    "    <BLONG>\n";  
    foreach($_SESSION["blong"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    foreach ($_POST["blong"] as $room)
    {
        $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
    }
    if ($_POST["blongnote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <BLongNote>".$_POST["slatenote"]."</BLongNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </BLONG>\n\n";
}


//SB
if (isset($_SESSION['sb']))
{
    $xmlstring = $xmlstring.
    "    <SB>\n";  
    foreach($_SESSION["sb"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    foreach ($_POST["sb"] as $room)
    {
        $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
    }
    if ($_POST["sbnote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <SBNote>".$_POST["sbnote"]."</SBNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </SB>\n\n";
}


//OB1
if (isset($_SESSION['ob1']))
{
    $xmlstring = $xmlstring.
    "    <OB1>\n";  
    foreach($_SESSION["ob1"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    if ($_POST["obnote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <OB1Note>".$_POST["obnote"]."</OB1Note>\n";
    }       
    $xmlstring = $xmlstring.
    "    </OB1>\n\n";
}


//SENDO
if (isset($_SESSION['endo']))
{
    $xmlstring = $xmlstring.
    "    <SENDO>\n";  
    foreach($_SESSION["endo"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    if ($_POST["sendonote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <SEndoNote>".$_POST["sendonote"]."</SEndoNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </SENDO>\n\n";
}


//SRAD
if (isset($_SESSION['srad']))
{
    $xmlstring = $xmlstring.
    "    <SRAD>\n";  
    foreach($_SESSION["srad"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }
    if ($_POST["sradnote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <SRadNote>".$_POST["sradnote"]."</SRadNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </SRAD>\n\n";
}


//PED
if (isset($_SESSION['ped']))
{
    $xmlstring = $xmlstring.
    "    <PED>\n";  
    foreach($_SESSION["ped"] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }

    if (isset ($_POST["ped"]))
    {
        foreach ($_POST["ped"] as $room)
        {
            $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
        }
    }
    if ($_POST["pednote"] != "")
    {
        $xmlstring = $xmlstring.
        "        <PedNote>".$_POST['pednote']."</PedNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </PED>\n\n";
}




//ERAS
if (isset($_SESSION['eras']))
{
    $xmlstring = $xmlstring.
    "    <ERAS>\n";  
    if (isset ($_SESSION['eras']))
    {
        foreach($_SESSION['eras'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }

    if (isset ($_POST["eras"]))
    {
        foreach ($_POST["eras"] as $room)
        {
            $xmlstring = $xmlstring."        <Room>".$room."</Room>\n";
        }
    }
    if ($_POST['erasnote'] != "")
    {
        $xmlstring = $xmlstring.
        "        <ERASNote>".$_POST["erasnote"]."</ERASNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </ERAS>\n\n";
}


//CVOR
if (isset($_SESSION['h1d']))
{
    $xmlstring = $xmlstring.
    "    <CVOR>\n";
    if (isset($_SESSION['h1d']))
    {
        foreach($_SESSION['h1d'] as $md)
        {
            $xmlstring = $xmlstring."        <H1PhysicianName>".$md."</H1PhysicianName>\n";
        }
    }
    if (isset($_SESSION['h2']))
    {
        foreach($_SESSION['h2'] as $md)
        {
            $xmlstring = $xmlstring."        <H2PhysicianName>".$md."</H2PhysicianName>\n";
        }
    }
    if ($_POST['cvornote'] != "CVOR Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <CVORNote>".$_POST["cvornote"]."</CVORNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </CVOR>\n\n";
}


//Orthopedic
if (isset($_SESSION['cohd']))
{
    $xmlstring = $xmlstring.
    "    <ORTHO>\n";
    if (isset($_SESSION['cohd']))
    {
        foreach($_SESSION['cohd'] as $md)
        {
            $xmlstring = $xmlstring."        <COHPhysicianName>".$md."</COHPhysicianName>\n";
        }
    }
    if (isset($_SESSION['soh']))
    {
        foreach($_SESSION['soh'] as $md)
        {
            $xmlstring = $xmlstring."        <SOHPhysicianName>".$md."</SOHPhysicianName>\n";
        }
    }
    if (isset($_SESSION['soh2']))
    {
        foreach($_SESSION['soh2'] as $md)
        {
            $xmlstring = $xmlstring."        <SOH2PhysicianName>".$md."</SOH2PhysicianName>\n";
        }
    }
    if (isset($_SESSION['bkoh']))
    {
        foreach($_SESSION['bkoh'] as $md)
        {
            $xmlstring = $xmlstring."        <BKOHPhysicianName>".$md."</BKOHPhysicianName>\n";
        }
    }
    if ($_POST['orthonote'] != "Ortho Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <OrthoNote>".$_POST["orthonote"]."</OrthoNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </ORTHO>\n\n";
}


//CLTOPS
if (isset($_SESSION['cops1']))
{
    $xmlstring = $xmlstring.
    "    <CLTOPS>\n";
    if (isset($_SESSION['cops1']))
    {
        foreach($_SESSION['cops1'] as $md)
        {
            $xmlstring = $xmlstring."        <COPS1PhysicianName>".$md."</COPS1PhysicianName>\n";
        }
    }
    if (isset($_SESSION['cops2']))
    {
        foreach($_SESSION['cops2'] as $md)
        {
            $xmlstring = $xmlstring."        <COPS2PhysicianName>".$md."</COPS2PhysicianName>\n";
        }
    }
    if ($_POST['opsnote'] != "CLTOPS Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <OpsNote>".$_POST["opsnote"]."</OpsNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </CLTOPS>\n\n";
}


//MidTn
if (isset($_SESSION['mid1']))
{
    $xmlstring = $xmlstring.
    "    <MIDTN>\n";
    if (isset($_SESSION['mid1']))
    {
        foreach($_SESSION['mid1'] as $md)
        {
            $xmlstring = $xmlstring."        <MidTnPhysicianName>".$md."</MidTnPhysicianName>\n";
        }
    }
    if (isset($_SESSION['mid2']))
    {
        foreach($_SESSION['mid2'] as $md)
        {
            $xmlstring = $xmlstring."        <MidTn2PhysicianName>".$md."</MidTn2PhysicianName>\n";
        }
    }
    if ($_POST['midnote'] != "Midtown Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <MidTnNote>".$_POST["midnote"]."</MidTnNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </MIDTN>\n\n";
}


//Spk
if (isset($_SESSION['spk']))
{
    $xmlstring = $xmlstring.
    "    <SPK>\n";
    if (isset($_SESSION['spk']))
    {
        foreach($_SESSION['spk'] as $md)
        {
            $xmlstring = $xmlstring."        <SpkPhysicianName>".$md."</SpkPhysicianName>\n";
        }
    }
    if (isset($_SESSION['spk2']))
    {
        foreach($_SESSION['spk2'] as $md)
        {
            $xmlstring = $xmlstring."        <Spk2PhysicianName>".$md."</Spk2PhysicianName>\n";
        }
    }
    if ($_POST['spknote'] != "SouthPark Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <SpkNote>".$_POST["spknote"]."</SpkNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </SPK>\n\n";
}


//Matthews
if (isset($_SESSION['cmatd']))
{
    $xmlstring = $xmlstring.
    "    <MATTHEWS>\n";
    if (isset($_SESSION['cmatd']))
    {
        foreach($_SESSION['cmatd'] as $md)
        {
            $xmlstring = $xmlstring."        <CMatPhysicianName>".$md."</CMatPhysicianName>\n";
        }
    }
    if (isset($_SESSION['sml']))
    {
        foreach($_SESSION['sml'] as $md)
        {
            $xmlstring = $xmlstring."        <SMLatePhysicianName>".$md."</SMLatePhysicianName>\n";
        }
    }
    if (isset($_SESSION['smat']))
    {
        foreach($_SESSION['smat'] as $md)
        {
            $xmlstring = $xmlstring."        <SMatPhysicianName>".$md."</SMatPhysicianName>\n";
        }
    }
    if ($_POST['mattnote'] != "Matthews Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <MatthewsNote>".$_POST["mattnote"]."</MatthewsNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </MATTHEWS>\n\n";
}


//MASC
if (isset($_SESSION['masc']))
{
    $xmlstring = $xmlstring.
    "    <MASC>\n";
    if (isset($_SESSION['masc']))
    {
        foreach($_SESSION['masc'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }
    if ($_POST['mascnote'] != "MASC Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <MASCNote>".$_POST["mascnote"]."</MASCNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </MASC>\n\n";
}


//Huntersville
if (isset($_SESSION['chntd']))
{
    $xmlstring = $xmlstring.
    "    <HUNTERSVILLE>\n";
    if (isset($_SESSION['chntd']))
    {
        foreach($_SESSION['chntd'] as $md)
        {
            $xmlstring = $xmlstring."        <CHntPhysicianName>".$md."</CHntPhysicianName>\n";
        }
    }
    if (isset($_SESSION['shnt']))
    {
        foreach($_SESSION['shnt'] as $md)
        {
            $xmlstring = $xmlstring."        <SHntPhysicianName>".$md."</SHntPhysicianName>\n";
        }
    }
    if (isset($_SESSION['shnt2']))
    {
        foreach($_SESSION['shnt2'] as $md)
        {
            $xmlstring = $xmlstring."        <Shnt2PhysicianName>".$md."</Shnt2PhysicianName>\n";
        }
    }
    if ($_POST['huntnote'] != "Huntersville Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <HuntersvilleNote>".$_POST["huntnote"]."</HuntersvilleNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </HUNTERSVILLE>\n\n";
}


//HASC
if (isset($_SESSION['hasc']))
{
    $xmlstring = $xmlstring.
    "    <HASC>\n";
    if (isset($_SESSION['hasc']))
    {
        foreach($_SESSION['hasc'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }
    if ($_POST['hascnote'] != "HASC Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <HASCNote>".$_POST["hascnote"]."</HASCNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </HASC>\n\n";
}


//ESC
if (isset($_SESSION['esc']))
{
    $xmlstring = $xmlstring.
    "    <ESC>\n";
    if (isset($_SESSION['esc']))
    {
        foreach($_SESSION['esc'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }
    if ($_POST['escnote'] != "ESC Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <ESCNote>".$_POST["escnote"]."</ESCNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </ESC>\n\n";
}


//Balantyne
if (isset($_SESSION['bal']))
{
    $xmlstring = $xmlstring.
    "    <BALLANTYNE>\n";
    if (isset($_SESSION['bal']))
    {
        foreach($_SESSION['bal'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }
    if ($_POST['balnote'] != "Ballantyne Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <BalNote>".$_POST["balnote"]."</BalNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </BALLANTYNE>\n\n";
}


//SOBA
if (isset($_SESSION['soba']))
{
    $xmlstring = $xmlstring.
    "    <SOBA>\n";
    if (isset($_SESSION['soba']))
    {
        foreach($_SESSION['soba'] as $md)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
        }
    }
    if ($_POST['sobanote'] != "SOBA Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <SOBANote>".$_POST["sobanote"]."</SOBANote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </SOBA>\n\n";
}


//PrePost
if (isset($_SESSION['prepost']))
{
    $xmlstring = $xmlstring.
    "    <PREPOST>\n";
    if (isset($_SESSION['prepost']))
    {
        foreach($_SESSION['prepost'] as $a)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$a[0]."</PhysicianName>\n";
            $xmlstring = $xmlstring."        <Assignment>".$a[1]."</Assignment>\n";
        }
    }
    if ($_POST['prepostnote'] != "Pre-/Post- Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <PrePostNote>".$_POST["prepostnote"]."</PrePostNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </PREPOST>\n\n";
}


//Unwanted Vacation
if (isset($_SESSION['unw']))
{
    $xmlstring = $xmlstring.
    "    <UNWANTED>\n";
    if (isset($_SESSION['unw']))
    {
        foreach($_SESSION['unw'] as $a)
        {
            $xmlstring = $xmlstring."        <PhysicianName>".$a[0]."</PhysicianName>\n";
            $xmlstring = $xmlstring."        <Assignment>".$a[1]."</Assignment>\n";
        }
    }
    if ($_POST['unwnote'] != "Unwanted Vacation Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <UnwNote>".$_POST["unwnote"]."</UnwNote>\n";
    }       
    $xmlstring = $xmlstring.
    "    </UNWANTED>\n\n";
}


//CALLS
$xmlstring = $xmlstring.
"    <CALLS>\n";

$xmlstring = $xmlstring.
"        <COR>\n";    
foreach($_SESSION['cor'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['cornote'] != "C OR Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["cornote"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </COR>\n\n";


$xmlstring = $xmlstring.
"        <COB>\n";    
foreach($_SESSION['cob'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['cobnote'] != "C OB Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["cobnote"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </COB>\n\n";

$xmlstring = $xmlstring.
"        <CMat>\n";    
foreach($_SESSION['cmat'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['cmat3note'] != "C Mat Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["cmat3note"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </CMat>\n\n";

$xmlstring = $xmlstring.
"        <CHnt>\n";    
foreach($_SESSION['chnt'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['chnt3note'] != "C Hnt Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["chnt3note"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </CHnt>\n\n";

$xmlstring = $xmlstring.
"        <H1>\n";    
foreach($_SESSION['h1c'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['ch1note'] != "H 1 Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["ch1note"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </H1>\n\n";

$xmlstring = $xmlstring.
"        <COH>\n";    
foreach($_SESSION['coh'] as $md)
{
    $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
}    
if ($_POST['cohnote'] != "C OH Notes: ")
{
    $xmlstring = $xmlstring.
    "        <Note>".$_POST["cohnote"]."</Note>\n";
}       
$xmlstring = $xmlstring.
"        </COH>\n\n";


if (isset($_SESSION['pedc']))
{
    $xmlstring = $xmlstring.
    "        <PedsCall>\n";    
    foreach($_SESSION['pedc'] as $md)
    {
        $xmlstring = $xmlstring."        <PhysicianName>".$md."</PhysicianName>\n";
    }    
    if ($_POST['pedcnote'] != "Peds Call Notes: ")
    {
        $xmlstring = $xmlstring.
        "        <Note>".$_POST["pedcnote"]."</Note>\n";
    }       
    $xmlstring = $xmlstring.
    "        </PedsCall>\n";
}

$xmlstring = $xmlstring.
"    </CALLS>\n\n";



//Meetings
if(isset($_SESSION['meeting']))
{
    $xmlstring = $xmlstring.
    "    <MEETINGS>\n";
    foreach($_SESSION['meeting'] as $a)
    {
        $xmlstring = $xmlstring."        <Meeting>".$a."</Meeting>\n";
    }
    $xmlstring = $xmlstring.
    "    </MEETINGS>\n\n";  
}

$xmlstring = $xmlstring.
"</Assignments>";
    fwrite($myfile,$xmlstring);

    
header('Location: ormassignments.php');
}
?>

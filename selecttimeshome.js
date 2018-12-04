var xmlHttp

function showTimesHome(str, str2)
{ 
xmlHttp=GetXmlHttpObject()
var url=""

if (xmlHttp==null)
{
    alert ("Browser does not support HTTP Request")
    return
}

if (str2=="1")
{
    url=url+"gettimeshome.php"
    url=url+"?q="+str
    url=url+"&sid="+Math.random()
    xmlHttp.onreadystatechange=stateChanged
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}
else
{
    url=url+"gettimeshome2.php"
    url=url+"?q="+str
    url=url+"&sid="+Math.random()
    xmlHttp.onreadystatechange=stateChanged2
    xmlHttp.open("GET",url,true)
    xmlHttp.send(null)
}

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
    document.getElementById("txtHint3").innerHTML=xmlHttp.responseText
 } 
}

function stateChanged2()
{
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 {
    document.getElementById("txtHint4").innerHTML=xmlHttp.responseText
 }
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}
}
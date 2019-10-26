var xmlHttp

function akcija(rb,takmicenje, takmicar, dana)
{
var si=document.getElementById("si"+rb).value;
var kat=document.getElementById("kat"+rb).value;
var dugme=document.getElementById("dugme"+rb).value;

var dani=0;
var checked;
for (i=1;i<=dana;i++)
{
if (document.getElementById("d"+rb+i).checked) checked=1
else checked=0
dani=dani+(parseFloat(document.getElementById("d"+rb+i).value)*checked);
}
if (dani==0)
{
	alert("Morate se prijaviti barem za jedan dan takmicenja !");
	return;
}
var sibr = parseInt(si);
if (sibr<1000 || sibr>9999999)
{
	alert("Ne ispravan broj SI cipa !");
	return;
}


xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 

if (dugme=="Prijavi")
{

var url="izvrsiPrijavu.php";
url=url+"?takmicenje="+takmicenje;
url=url+"&takmicar="+takmicar;
url=url+"&kategorija="+kat;
url=url+"&si="+si;
url=url+"&dani="+dani;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange = function(){if(xmlHttp.readyState==4){obradaPrijave(rb,dana)}};
xmlHttp.open("GET",url,true);
xmlHttp.send(null);

document.getElementById("dugme"+rb).value="Obrada";
document.getElementById("dugme"+rb).disabled=true;
document.getElementById("si"+rb).disabled=true;
document.getElementById("kat"+rb).disabled=true;
document.getElementById("red"+rb).className ="obrada";
for (i=1;i<=dana;i++)
	document.getElementById("d"+rb+i).disabled=true;


}
else
{
document.getElementById("dugme"+rb).value="Obrada";
document.getElementById("dugme"+rb).disabled=true;
document.getElementById("red"+rb).className ="obrada";

var url="izvrsiOdjavu.php";
url=url+"?takmicenje="+takmicenje;
url=url+"&takmicar="+takmicar;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange = function(){if(xmlHttp.readyState==4){obradaOdjave(rb,dana)}};
xmlHttp.open("GET",url,true);
xmlHttp.send(null);

}

} 

function obradaPrijave(rb,dana) 
{ 
if (xmlHttp.responseText=="OK")
{
	document.getElementById("dugme"+rb).value="Odjavi";
	document.getElementById("red"+rb).className ="prijavljen";
}else
{
	document.getElementById("dugme"+rb).value="Prijavi";
	document.getElementById("red"+rb).className ="greska";
	document.getElementById("si"+rb).disabled=false;
	document.getElementById("kat"+rb).disabled=false;
	for (i=1;i<=dana;i++)
		document.getElementById("d"+rb+i).disabled=false;

}	
	document.getElementById("dugme"+rb).disabled=false;
	document.getElementById("akcija"+rb).innerHTML=xmlHttp.responseText;
	

}

function obradaOdjave(rb,dana) 
{ 
	document.getElementById("dugme"+rb).value="Prijavi";
	document.getElementById("dugme"+rb).disabled=false;
	document.getElementById("si"+rb).disabled=false;
	document.getElementById("kat"+rb).disabled=false;
	document.getElementById("akcija"+rb).innerHTML=xmlHttp.responseText;
	document.getElementById("red"+rb).className ="neprijavljen";
	for (i=1;i<=dana;i++)
		document.getElementById("d"+rb+i).disabled=false;


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
  // Internet Explorer
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

function shownovi()
{
document.getElementById("novo").style.display = 'block';
document.getElementById("novi").style.display = 'none';
}

function novitakmicar(klub,takId)
{
var ime=document.getElementById("nime").value;
var prezime=document.getElementById("nprezime").value;
var si=document.getElementById("nsi").value;
var beleska=document.getElementById("nbeleska").value;
var kategorija=document.getElementById("nkategorija").value;

xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 

var url="ubaciTakmicara.php";
url=url+"?ime="+ime;
url=url+"&prezime="+prezime;
url=url+"&kategorija="+kategorija;
url=url+"&si="+si;
url=url+"&beleska="+beleska;
url=url+"&klub="+klub;
url=url+"&takId="+takId;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange = function(){if(xmlHttp.readyState==4){ubacenTakmicar()}};
xmlHttp.open("GET",url,true);
xmlHttp.send(null);

}

function ubacenTakmicar()
{
	window.location.reload()
}

function prijavaTakmicara(kl,tak)
{
var klub=kl.options[kl.selectedIndex].value;

xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 

var url="prijava.php";
url=url+"?takmicenje="+tak;
url=url+"&kid="+klub;
url=url+"&sid="+Math.random();

xmlHttp.onreadystatechange = function(){if(xmlHttp.readyState==4){obradaGetKlub()}};
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
} 


function obradaGetKlub() 
{ 
	document.getElementById("prijava").innerHTML=xmlHttp.responseText;

}


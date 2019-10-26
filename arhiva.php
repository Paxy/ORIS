<?PHP
session_start();
ob_start();
error_reporting(E_ERROR | E_PARSE);

include "hedder.php";

echo "<div style=\"margin: 30px;\"><h1>Arhiva Bodovanja</h1><p><b> |<a href=/arhiva/2012.zip>2012</a>| |<a href=/arhiva/2013.zip>2013</a>| |<a href=/arhiva/2014.zip>2014</a>| </b></div><p><p>";
echo "<div style=\"margin: 30px;\"><h1><a href=https://drive.google.com/drive/folders/0B7ivvkXFIhbsbTdnYTdKaUF4bFU>Arhiva rezultata Jugoslavije i Srbije od 1977. godine</a></h1><p> dokumentovao Ilija Dimitrijević</div><p><p>";
echo "<IFRAME SRC=/arhiva/ WIDTH=820 HEIGHT=1000 SCROLLING=YES style=\"
    margin-left: 24px;
\">
If you can see this, your browser doesn't 
understand IFRAME.  However, we'll still 
<A HREF=\"/arhiva/\">link</A> 
you to the file.
</IFRAME>";
 
 include "footer.php";
 
 
 
?>
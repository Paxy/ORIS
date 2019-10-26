<?PHP


error_reporting(E_ERROR | E_PARSE);
require_once( 'functions.php' ); 

include "hedder.php";

echo "<IFRAME SRC=\"bodovanjeKlub.php\" WIDTH=820 HEIGHT=860 SCROLLING=YES>
If you can see this, your browser doesn't 
understand IFRAME.  However, we'll still 
<A HREF=\"bodovanjeKlub.php\">link</A> 
you to the file.
</IFRAME>";
echo "<br><br><A HREF=\"bodovanjeKlub.php\" target=_new>Prikaz bodova u novom prozoru</A><br><br> ";
include "footer.php";


?>
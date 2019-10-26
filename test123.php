<?php
error_reporting(E_ERROR | E_PARSE);

require_once( 'functions.php' ); 



session();



include "hedder.php";

echo '<script>
function test()
{

alert("tesT");
var event = document.createEvent("MouseEvents");
  var ev = document.createEvent("MouseEvent");
    var el = document.elementFromPoint(1340,154);
    ev.initMouseEvent(
        "click",
        true /* bubble */, true /* cancelable */,
        window, null,
        1340,154, 0, 0, /* coordinates */
        false, false, false, false, /* modifier keys */
        0 /*left*/, null
    );
    el.dispatchEvent(ev);
}
</script>
<button onclick="test()">Click me</button> ';


$vreme='1:13:29';
$split=split(':', $vreme);
			
			if (count($split)>2)
			{
			$minut=intval($split[0])*60+intval($split[1]);
			$sekund=intval($split[2]);
			}
			else
			{
			$minut=intval($split[0]);
			$sekund=intval($split[1]);
			}		
			
			print_r($split);
			echo $minut;
			echo $sekund;


include "footer.php";



?>
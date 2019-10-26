<?PHP

require_once( 'functions.php' ); 
session();

echo "<br><br>";
echo "<a href=takmicenja.php>Takmi&#x010D;enja</a><br>";
echo "<a href=bodovanja.php>Bodovanje</a><br>";
echo "<a href=bodovanjaKlubova.php>Bodovanje klubova</a><br>";
echo "<a href=bodovanjaV2.php>BodovanjeV2 (Beta)</a><br>";
echo "<a href=rss.php>Mail-lista</a><br>";
echo "<a href=karte.php?god=".date('Y').">Orijentiring karte</a><br>";
echo "<a href=vreme.php>Vremenska prognoza</a><br>";
echo "<a href=scoresum.php>ScoreSum</a><br>";
echo "<a href=display.php>Display</a><br>";
echo "<a href=\"http://orijentiring.rs/site/wp-content/uploads/2019/02/kalendar_oss_2019.pdf\" target=_new>Kalendar takmi&#x010D;enja</a><br>";
//echo "<a href=\"http://forum.orijentiring.rs/\" target=_new>Forum</a><br>";
echo "<a href=\"video.php\">Video</a><br>";
echo "<a href=\"split-visualiser/\">Split Visualiser</a><br>";
echo "<a href=\"arhiva.php\">Arhiva</a><br>";
echo "<a href=\"srr.htm\">SRR</a><br>";

if (IsSet($_SESSION['klubId']) && $_SESSION['klubId']>1000) echo "<br><a href=admin.php>Admin panel</a><br>";

echo "<br><br>";
include "login.php";


echo '<br><br><style>select.goog-te-combo {width: 150px;} span {white-space: normal !important;}</style><div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: \'sr\', autoDisplay: false}, \'google_translate_element\');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>';
?>

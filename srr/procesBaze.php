<?php
$filename="baza.csv";

$takmicari=array();

if (($handle = fopen($filename, "r")) !== FALSE) {
        $data = fgetcsv($handle,0, ";");
		$head = implode(",", $data);
		 if (strpos($head,"Chipno")) //OE2013
		 while (($data = fgetcsv($handle,0, ";")) !== FALSE) 
         {
            $prezime=utf($data[5]);
    	    $ime=utf($data[6]);
            $si=$data[3];
            $klub=utf($data[19]);
            $kat=utf($data[25]);
            $start=$data[11];
            if(intval($si)>0)
                $takmicari[$si]=array("ime"=>$ime,"prezime"=>$prezime,"klub"=>$klub,"kat"=>$kat,"start"=>$start);

         } 
         elseif (strpos($head,"Chip"))  //OE
		 while (($data = fgetcsv($handle,0, ";")) !== FALSE) 
		 {
            $prezime=utf($data[3]);
    	    $ime=utf($data[4]);
            $si=$data[1];
            $klub=utf($data[15]);
            $kat=utf($data[18]);
            $start=$data[9];
            if(intval($si)>0)
                $takmicari[$si]=array("ime"=>$ime,"prezime"=>$prezime,"klub"=>$klub,"kat"=>$kat,"start"=>$start);
         }


}

function utf($msg)
{
if (preg_match('!!u', $msg)) return $msg; //UTF-8
return w1250_to_utf8($msg);
}
function w1250_to_utf8($text) {
    $map = array(
        chr(0x8A) => chr(0xA9),
        chr(0x8C) => chr(0xA6),
        chr(0x8D) => chr(0xAB),
        chr(0x8E) => chr(0xAE),
        chr(0x8F) => chr(0xAC),
        chr(0x9C) => chr(0xB6),
        chr(0x9D) => chr(0xBB),
        chr(0xA1) => chr(0xB7),
        chr(0xA5) => chr(0xA1),
        chr(0xBC) => chr(0xA5),
        chr(0x9F) => chr(0xBC),
        chr(0xB9) => chr(0xB1),
        chr(0x9A) => chr(0xB9),
        chr(0xBE) => chr(0xB5),
        chr(0x9E) => chr(0xBE),
        chr(0x80) => '&euro;',
        chr(0x82) => '&sbquo;',
        chr(0x84) => '&bdquo;',
        chr(0x85) => '&hellip;',
        chr(0x86) => '&dagger;',
        chr(0x87) => '&Dagger;',
        chr(0x89) => '&permil;',
        chr(0x8B) => '&lsaquo;',
        chr(0x91) => '&lsquo;',
        chr(0x92) => '&rsquo;',
        chr(0x93) => '&ldquo;',
        chr(0x94) => '&rdquo;',
        chr(0x95) => '&bull;',
        chr(0x96) => '&ndash;',
        chr(0x97) => '&mdash;',
        chr(0x99) => '&trade;',
        chr(0x9B) => '&rsquo;',
        chr(0xA6) => '&brvbar;',
        chr(0xA9) => '&copy;',
        chr(0xAB) => '&laquo;',
        chr(0xAE) => '&reg;',
        chr(0xB1) => '&plusmn;',
        chr(0xB5) => '&micro;',
        chr(0xB6) => '&para;',
        chr(0xB7) => '&middot;',
        chr(0xBB) => '&raquo;',
    );
    return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
}
?>

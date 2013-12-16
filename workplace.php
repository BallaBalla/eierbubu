<?php
	//Include:
	include('./cfg.php');
	include('./lib.php');


	//WorkZone		                
	$wuschi=login($usrname, $password_crypt, $welt);
	$dorfnr=getdnr();
	$reportout=report($welt, $dorfnr);
	$attacks=getattackreports($reportout);
	
	//echo $attacks[1]."\n";
?>

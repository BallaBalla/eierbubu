<?php
	//Include:
	include('./cfg.php');
	include('./lib.php');


	//WorkZone		                
	$wuschi=login($usrname, $password_crypt);
	$dorfnr=getdnr();
	$reportout=report($welt, $dorfnr);
	$attacks=getattackreports($reportout);
	logout($dorfnr);
	//echo $attacks[1]."\n";
?>

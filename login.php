<?php	          	 
	$usrname="bobofoetz123";
	$passwoede="69ed84319e124796079e4447c93db49268f1c9f2";
	$welt="98";
	$dorfnr="119400";
	          	 	          	 
	//LOGIN -------------------------------------------------------------------------
	function login($usrname, $passwoede, $welt) {
		global $ch; //variable auch ausserhalb der funktion verfuegbar machen
		
		$ch = curl_init(); //curl session eroeffnen
	                		
	    curl_setopt($ch, CURLOPT_URL, "http://www.die-staemme.de/index.php");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/staco.txt');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $output = curl_exec($ch);

	    curl_setopt($ch, CURLOPT_URL, "http://www.die-staemme.de/index.php?action=login&server_de$welt");
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$usrname&password=$passwoede");
	    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/staco.txt');
	    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/staco.txt');
	    $output = curl_exec($ch);

	    //return $output;
	}
	                
	//DOWNLOAD REPORT --------------------------------------------------------------
	function report($welt, $dorfnr) {
		global $ch;
		
		curl_setopt($ch, CURLOPT_URL, "http://de$welt.die-staemme.de/game.php?village=$dorfnr&mode=attack&screen=report");
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/staco.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/staco.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$output = curl_exec($ch);
		
		curl_close($ch); //curl session beenden ::: temporaer in dieser funktion. spaeter in logout funktion!!!
		
		return $output;                
	}
	
	//SEARCH ATTACK REPORTS -------------------------------------------------------
	function getattackreports($reportout) {
		$afterbox=explode('checkbox" />', $reportout);
		$b=1;
		//echo "startwhile 1\n";
		while($afterbox[$b]){
			$attacks=explode('</td>', $afterbox[$b]);
			$a=0;
			//echo "startwhile 2\n";
			while($attacks[$a]){
				if(strpos($attacks[$a], 'Barbarendorf') !== false && strpos($attacks[$a], '(neu)') !== false) {
					//echo "enter if 2 \n";
					$link=explode('<a href="', $attacks[$a]);
					$link=explode('">', $link[1]);		
					$links[] = $link[0];	
				}
			$a++;
			}
		$b++;	
		}
	return $links;
	}
		                
	$wuschi=login($usrname, $passwoede, $welt);
	$reportout=report($welt, $dorfnr);
	$attacks=getattackreports($reportout);
	
	echo $attacks[1]."\n";
?>

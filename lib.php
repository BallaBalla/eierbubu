<?php	          	 
          	 	          	 
	//LOGIN -------------------------------------------------------------------------
	function login($usrname, $password_crypt, $welt) {
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
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$usrname&password=$password_crypt");
	    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/staco.txt');
	    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/staco.txt');
	    $output = curl_exec($ch);

	    //return $output;
	}
	//GET DorfNr.-------------------------------------------------------------------
	function getdnr(){
		global $ch, $welt, $debug;
		if($debug==2){echo "DEBUG: getdnr: Start Function getdnr.\n";}
		
		// Check $ch is Ok_
		if($debug >= 1 && !$ch){echo "ERROR: getdnr: [\$ch is NOT set.]\n";}
		if($debug == 2 &&  $ch){echo "DEBUG: getdnr: \$ch is set.\n";}
		

		// Willkommenseite aufrufen:
		curl_setopt($ch, CURLOPT_URL, "http://de$welt.die-staemme.de/game.php?screen=welcome&intro&oscreen=overview");
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//DorfNummer ausschneiden:
		$output = curl_exec($ch);
		$temp=explode(',"village":{"id":', $output);
		$dnr_arr=explode(',"name":', $temp[1]);
		//Check DorfNum:
		if(preg_match("/\A[0-9]+\z/", $dnr_arr[0])){
			if($debug == 2){echo "DEBUG: getdnr: DorfNr. are just a Number (That means it's OK).\n";}
		}else{
			if($debug >= 1){echo "ERROR: getdnr: DorfNr. is NOT a Number (That means it's NOT OK).\n";}
		}
		//Debug Output
		if($debug == 2){echo "DEBUG: getdnr: DorfNr.: [".$dnr_arr[0]."]\n";}
		if($debug == 2){echo "DEBUG: getdnr: End of Function getdnr.\n";}
		//Ausgabe der Dorfnummer:
		return $dnr_arr[0];
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
?>

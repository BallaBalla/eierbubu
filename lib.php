<?php	     
	//Debug -------------------------------------------------------------------------
	function debug($REPORTLEVEL, $MESSAGE) {
		global $debug, $html_output, $output_to_file, $output_file_path; 
		// Output in HTML?
		if($html_output==1){
			$end="<br>";
		}else{
			$end="";
		}
		//Output
		if($output_to_file==1){
			$handle = fopen("$output_file_path", "a");
			if($REPORTLEVEL==1 && $debug>=1){
				fwrite($handle, "ERROR: ".$MESSAGE." ".$end."\n");
			}
			if($REPORTLEVEL==2 && $debug==2){
				fwrite($handle, "DEBUG: ".$MESSAGE." ".$end."\n");
			}
			fclose($handle);
		}else{
			if($REPORTLEVEL==1 && $debug>=1){
				echo "ERROR: ".$MESSAGE." ".$end."\n";
			}
			if($REPORTLEVEL==2 && $debug==2){
				echo "DEBUG: ".$MESSAGE." ".$end."\n";
			}
		}
	}
	function err($MESSAGE){
		debug(1, $MESSAGE);
	}	
	function inf($MESSAGE){
		debug(2, $MESSAGE);
	}     	 
          	 	          	 
	//LOGIN -------------------------------------------------------------------------
	function login($usrname, $password_crypt) {
		global $ch, $welt, $pre_url, $cookie_file; //variable auch ausserhalb der funktion verfuegbar machen
		
		$ch = curl_init(); //curl session eroeffnen
	                		
	    curl_setopt($ch, CURLOPT_URL, "http://www.die-staemme.de/index.php");
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $output = curl_exec($ch);

	    curl_setopt($ch, CURLOPT_URL, "http://www.die-staemme.de/index.php?action=login&server_de$welt");
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$usrname&password=$password_crypt");
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	    $output = curl_exec($ch);

	    //return $output;
	}
	//Logout --------------------------------------------------------------
	function logout($dorfnr) {
		global $ch, $pre_url, $cookie_file;

		// Check $ch is Ok
		debug(2, "logout: Start Function logout with Parameter [DorfNumber:$dorfnr] and the global Variables [PREURL:$pre_url, COOKIEFILE:$cookie_file].");
		if(!$ch){debug(1,"logout: [\$ch is NOT set.]");}
		if( $ch){debug(2,"logout: \$ch is set.");}

		//Logout from Staemme
		curl_setopt($ch, CURLOPT_URL, "http://$pre_url.die-staemme.de/game.php?village=$dorfnr&action=logout");
		$output = curl_exec($ch);

		//Debug
		if(preg_match("/Du hast dich erfolgreich ausgeloggt/i", $output)){
			debug(2, "logout: Logout is OK.");
		}else{
			debug(1, "logout: Logout FAILS.");
		}

		//Close Curl Session
		curl_close($ch); 

		//Remove Cookie
		unlink($cookie_file);  
		debug(2, "logout: End of Function logout.");         
	} 
	//GET DorfNr.-------------------------------------------------------------------
	function getdnr(){
		global $ch, $pre_url;
		debug(2, "getdnr: Start Function getdnr with the global Variables [PREURL:$pre_url].");
		
		// Check $ch is Ok_
		if(!$ch){debug(1,"getdnr: [\$ch is NOT set.]");}
		if( $ch){debug(2,"getdnr: \$ch is set.");}

		// Willkommenseite aufrufen:
		curl_setopt($ch, CURLOPT_URL, "http://$pre_url.die-staemme.de/game.php?screen=welcome&intro&oscreen=overview");
		curl_setopt($ch, CURLOPT_POST, FALSE);
		//DorfNummer ausschneiden:
		$output = curl_exec($ch);
		$temp=explode(',"village":{"id":', $output);
		$dnr_arr=explode(',"name":', $temp[1]);
		//Check DorfNum:
		if(preg_match("/\A[0-9]+\z/", $dnr_arr[0])){
			debug(2, "getdnr: DorfNr. are just a Number (That means it's OK).");
		}else{
			debug(1, "getdnr: DorfNr. is NOT a Number (That means it's NOT OK).");
		}
		//Debug Output
		debug(2,"getdnr: DorfNr.: [".$dnr_arr[0]."]");
		debug(2,"getdnr: End of Function getdnr.");
		//Ausgabe der Dorfnummer:
		return $dnr_arr[0];
	}
	                
	//DOWNLOAD REPORT --------------------------------------------------------------
	function report($pre_url, $dorfnr) {
		global $ch, $cookie_file;
		
		curl_setopt($ch, CURLOPT_URL, "http://$pre_url.die-staemme.de/game.php?village=$dorfnr&mode=attack&screen=report");
		curl_setopt($ch, CURLOPT_POST, FALSE);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		$output = curl_exec($ch);

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

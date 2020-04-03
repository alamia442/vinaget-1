<?php
class dl_multishare_cz extends Download {
	private function ConvertSize($filesize) {
	    $filesize = str_replace(',', '.', $filesize);
	    $filesize = preg_replace('/(\(|\)|)/', '', $filesize);
	    if (preg_match('/^([0-9]{1,4}+(\.[0-9]{1,2})?)/', $filesize, $value)) {
	        if (stristr($filesize, "TB"))    
	            $value = $value[1] * 1024 * 1024 * 1024 * 1024;
	        elseif (stristr($filesize, "GB")) 
	            $value = $value[1] * 1024 * 1024 * 1024;
	        elseif (stristr($filesize, "MB")) 
	            $value = $value[1] * 1024 * 1024;
	        elseif (stristr($filesize, "KB")) 
	            $value = $value[1] * 1024;
	        elseif (stristr($filesize, "B"))  
	            $value = $value[1];
	    }
	    else 
	        $value = 0;
	    return $value;
	}
	public function CheckAcc($cookie) {
		if (stristr($cookie, "uid") || stristr($page, "pass")) 
			$cookie = preg_replace('/\suid=.*;\supass=.*;/i', '', $cookie);
		$page = $this->lib->curl("https://www.multishare.cz/en/uzivatele/prehled", $cookie, '');
		if (preg_match('/class="tooltip database" title=".*">(.*?)<\/a><\/li>/i', $page, $temp)) {
    		$bandwidth = $this->ConvertSize($temp[1]);
    		if ($bandwidth > 0) {
    			$uID = $this->lib->cut_str($page, '<div id="mms-uid">', '</div>');
    			$uPass = $this->lib->cut_str($page, '<div id="mms-upass">', '</div>');
    			$this->save($cookie . " uid={$uID}; upass={$uPass};");
    			return array(true, "Bandwidth left: {$bandwidth}");
    		}
    		else
    			array(false, "accfree");
		}
		else 
			return array(false, "accinvalid");
	}
	public function Login($user, $pass) {
		$page = $this->lib->curl("https://www.multishare.cz/en/do-login", "lang=en", "jmeno={$user}&heslo={$pass}&send=Login");;
		$cookie = $this->lib->GetCookies($page);
		return $cookie;
	}
	public function Leech($url) {
		if (preg_match('/\suid=(.*);\supass=(.*);/i', $this->lib->cookie, $apiToken)) {
			/* Check url */
			$page = $this->lib->curl("https://s5.multishare.cz/html/mms_ajax.php", '', "link=" . urlencode($url) . "&json=1");
			if (stristr($page, "neznam"))
				$this->error("notsupport", true, false, 2);
			/* Leech */
			$page = $this->lib->curl("https://s5.multishare.cz/html/mms_process.php?u_ID={$apiToken[1]}&u_hash={$apiToken[2]}&link=" . urlencode($url), $this->lib->cookie, '', 1, 0, 1);
			if (stristr($page, "https://www.multishare.cz/chyba-stahovani?typ=nepodporovana-adresa"))
				$this->error("errorget", true, false, 2);
			else if (stristr($page, "https://www.multishare.cz/en/chyba-stahovani?typ=nedostatecny-kredit"))
				$this->error("LimitAcc", true, false, 2);
			else if ($this->isredirect($page))
				return trim($this->redirect);
		}
		return false;
	}
}
?>
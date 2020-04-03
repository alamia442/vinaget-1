<?php
class dl_alfafile_net extends Download {
	public function CheckAcc($cookie) {
		$page = $this->lib->curl("https://alfafile.net/user/payment", "lang=en;{$cookie}", '');
		if (stristr($page, '<p>Free</p>') && !stristr($page, 'Next Billing Date'))  
			return array(false, "accfree");
		else if (stristr($page, "Current plan") || stristr($page, "Next Billing Date")) 
			return array(true, "Premium Expires On: " . $this->lib->cut_str($this->lib->cut_str($page, "Next Billing Date</strong>", "</div>"), '<span class="date">', "</span>") . "<br/> Current Plan: " . $this->lib->cut_str($this->lib->cut_str($page, "Current plan</h3>", "</div>"), "<p>", "</p>"));
		else 
			return array(false, "accinvalid");
	}
       
	public function Login($user, $pass) {
		$page = $this->lib->curl("https://alfafile.net/user/login/?url=%2F", "lang=en", "email={$user}&password={$pass}&remember_me=1");
		$cookie = "lang=en;" . $this->lib->GetCookies($page);
		return $cookie; 
	}
	
	public function Leech($url) {
		$page = $this->lib->curl($url, $this->lib->cookie, '');
        if (stristr($page, "<strong>404</strong>")) 
        	$this->error("dead", true, false, 2);
		else if (!$this->isredirect($page)) {
			if (preg_match('/href="(https?:\/\/.+alfafile\.net\/dl\/.+)" class/i', $page, $match)) 
				return trim($match[1]);
		}
		else 
			return trim($this->redirect);
		return false;
	}
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* AlfaFile Download Plugin KulGuY [06.05.2015]
* Downloader Class By [FZ]
*/
?>
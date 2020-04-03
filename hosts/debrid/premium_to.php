<?php
class dl_premium_to extends Download {
	public function CheckAcc($cookie) {
		$page = $this->lib->curl("http://premium.to/account.php", $cookie, '', 1, 1, "http://premium.to/");
		if (stristr($page, "HTTP/1.1 400"))
			return array(false, "accinvalid");
		else {
			$bandwith = $this->lib->cut_str($page, '"x":', ',');
			if ($bandwith <= 0)
				return array(false, "accfree");
			return array(true, "Bandwith left {$bandwith} MB");
		}
	} 
	public function Login($user, $pass) {
		$page = $this->lib->curl("http://premium.to/login.php", '', '{"u": "' . $user . '", "p": "' . $pass . '", "r": false}', 1, 0, "http://premium.to/");
		$cookie = $this->lib->GetCookies($page);
		return $cookie;
	}
	public function Leech($url) {
		$page = $this->lib->curl("http://premium.to/getfile.php?link=" . urlencode($url), $this->lib->cookie, '');
		if (stristr($page, "File hosting service not supported") || stristr($page, "No premium account available"))
			$this->error("notsupport", true, false, 2);
		else if ($this->isredirect($page))
			return trim($this->redirect);
		return false;
	}
}
?>
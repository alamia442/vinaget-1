<?php
class dl_mega_debrid_eu extends Download {
	public function CheckAcc($cookie) {
		if (stristr($cookie, "vip=0;"))
			return array(false, "accfree");
		else
			return array(true, "Until " . date('d/m/Y', $this->lib->cut_str($cookie, "vip=", ';')));
	}
	public function Login($user, $pass) {
		$page = $this->lib->curl("https://www.mega-debrid.eu/api.php?action=connectUser&login={$user}&password={$pass}", '', '', 0);
		$json = json_decode($page, true);
		if ($json['response_code'] == "ok" || $json['response_text'] == "User logged")
			return "token={$json['token']}; vip={$json['vip_end']};";
		else if ($json['response_code'] == "BLOCKED_USER" || $json['response_text'] == "Server detected")
			$this->error("blockIP", true, true, 2);	
		else
			$this->error("accinvalid", true, true, 2);
	}
	public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		if ($pass)
			$this->error("notsupport", true, false, 2);
		$token = $this->lib->cut_str($this->lib->cookie, "token=", ";");
		$page = $this->lib->curl("https://www.mega-debrid.eu/api.php?action=getLink&token={$token}", '', "link={$url}", 0);
		$json = json_decode($page, true);
		if ($json['response_code'] == "UNRESTRICTING_ERROR") {
			if ($json['response_text'] == "Error occurred during unrestring link:Type de lien non supporté ou hébergeur non supporté")
				$this->error("notsupport", true, false, 2);
			else
				$this->error("errorget", true, false, 2);
		}
		else if ($json['response_code'] == "ok")
			return trim($json['debridLink']);
		return false;
	}
}
?>
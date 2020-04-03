<?php
class dl_real_debrid_com extends Download {
	public function CheckAcc($cookie) {
		/* Get Token */
		if (!stristr($cookie, "token"))
			return array(false, "accinvalid");
		$token = $this->lib->cut_str($cookie, 'token=', ';');
		/* Request */
		$page = $this->lib->curl("https://api.real-debrid.com/rest/1.0/user/method?auth_token=" . $token, '', '', 0);
		if (isset($page) && $page != '') {
			$json = json_decode($page, true);
			if ($json['type'] == "premium")
				return array(true, "Until " . $json['expiration']);
			else if ($json['type'] == "free")
				return array(false, "accfree");
			else
				return array(false, "accinvalid");
		}
		else
			return array(false, "erroracc");	
	}
	public function Leech($url) {
		/* Get Token */
		$token = $this->lib->cut_str($this->lib->cookie, 'token=', ';');
		list($url, $pass) = $this->linkpassword($url);
		/* Request */
		if ($pass)
			$page = $this->lib->curl("https://api.real-debrid.com/rest/1.0/unrestrict/link/method?auth_token=" . $token, '', "link={$url}&password={$pass}", 0);
		else
			$page = $this->lib->curl("https://api.real-debrid.com/rest/1.0/unrestrict/link/method?auth_token=" . $token, '', "link={$url}", 0);
		if (isset($page) && $page != '') {
			$json = json_decode($page, true);
			if (isset($json['download']))
				return trim($json['download']);
			else
				$this->error($json['error'], 0, 0, 1);
		}

		return false;
	}
}
?>
<?php
class dl_alldebrid_com extends Download {
	public function CheckAcc($cookie) {
		$token = $this->lib->cut_str($cookie, "token=", ';');
		$page = $this->lib->curl("https://api.alldebrid.com/user/login?agent=" . urlencode($this->lib->UserAgent) . "&token={$token}", '', '', 0);
		$json = json_decode($page, true);
		if (json_last_error() == JSON_ERROR_NONE) {
			if (isset($json['error']))
				return array(false, "accinvalid");
			if ($json['user']['isPremium']) {
				return array(true, "Until: " . date('d/m/Y', $json['user']['premiumUntil']));
			}
			else
				return array(false, "accfree");
		}
		else
			return array(false, "accinvalid");
	}
	public function Login($user, $pass) {
		$page = $this->lib->curl("https://api.alldebrid.com/user/login?agent=" . urlencode($this->lib->UserAgent) . "&username=" . urlencode($user). "&password=" . urlencode($pass), '', '', 0);
		$json = json_decode($page, true);
		if ((json_last_error() == JSON_ERROR_NONE)) 
			if (isset($json['token']))
				return "token={$json['token']};";
			else if (isset($json['errorCode']) && $json['errorCode'] == 3)
				$this->lib->error("blockip", true, true, 2);
		return "token=aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa;";
	}
	public function Leech($url) {
		$url = preg_replace("/(&|\?).*=[^&]*$/", '', $url);
		list($url, $pass) = explode('|', $url);
		if ($pass)
			$this->lib->error("linkpass", true, false, 2);
		$token = $this->lib->cut_str($this->lib->cookie, "token=", ';');
		$page = $this->lib->curl("https://api.alldebrid.com/link/unlock?agent=" . urlencode($this->lib->UserAgent) . "&token={$token}&link=" . urlencode($url), '', '', 0);
		$json = json_decode($page, true);
		if (json_last_error() == JSON_ERROR_NONE) {
			if ($json['infos']['link'])
				return trim($json['infos']['link']);
			else
				$this->lib->error($json['error'], true, false, 2);
		}
		return false;
	}
}
?>
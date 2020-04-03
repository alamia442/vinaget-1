<?php
class dl_bestdebrid_com extends Download {
	public function CheckAcc($cookie) {
		/* Request */
		$page = $this->lib->curl("https://bestdebrid.com/profile", $cookie, '', 0);
		if (stristr($page, "Debrid Membership expiration") && stristr($page, 'class="fa fa-calendar"></span>'))
			return array(true, "Until " . $this->lib->cut_str($page, "expiration :", "</p>"));
		else if (stristr($page, "Member since") && stristr($page, 'class="fa fa-info"></span>'))
			return array(false, "accfree");
		else 
			return array(false, "accinvalid");
	}
	public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		/* Request */
		$page = $this->lib->curl("https://bestdebrid.com/api/v1/generateLink", $this->lib->cookie, "link={$url}", 0);
		if (isset($page) && $page != '') {
			$json = json_decode($page, true);
			if (isset($json['link']))
				return trim($json['link']);
			else
				$this->error($json['message'], true, false, 1);
		}
		return false;
	}
}
?>
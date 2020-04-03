<?php
class dl_uploaded_net extends Download {
	public function PreLeech($url) { 
		if (stristr($url, "/f/")) {
			$page = $this->lib->curl($url, '', '');
			$page = $this->lib->cut_str($page, '<table id="fileList">', "</table>");
			$FID = explode('<h2><a href="file', $page);
			$maxfile = count($FID);
			for ($i = 1; $i < $maxfile; $i++) {
				preg_match('%\/(.+)\/from\/(.*)%U', $FID[$i], $code);
				$list = "<a href=http://uploaded.net/file/{$code[1]}>http://uploaded.net/file/{$code[1]}/</a><br/>";
				echo $list;
			}
			exit;
		}
	}  
	public function CheckAcc($cookie) {
		$page = $this->lib->curl("http://uploaded.net/", $cookie, '');
		$data = $this->lib->curl("http://uploaded.net/file/wojimfnt", $cookie, ''); 
		if (stristr($data, "You used too many different IPs")) 
			return array(false, "blockAcc");
		else if (stristr($data, "Hybrid-Traffic is completely exhausted")) 
			return array(false, "LimitAcc");
		else if (stristr($page, '<a href="register"><em>Premium</em></a>')) 
			return array(true, $this->lib->cut_str($this->lib->cut_str($page, "Duration:</td>", "/th>"), "<th>", "<") . "<br/>Bandwidth available: " . $this->lib->cut_str($page, '<th colspan="2"><b class="cB">', '</b></th>'));
		else if (stristr($page, '<li><a href="logout">Logout</a></li>')) 
			return array(false, "accfree");
		else 
			return array(false, "accinvalid");
	}
	public function Login($user, $pass) {
		$page = $this->lib->curl("http://uploaded.net/io/login", '', "id={$user}&pw={$pass}");
		$cookie = $this->lib->GetCookies($page);
		return $cookie;
	}  
	public function Leech($url) {
		$page = $this->lib->curl($url, $this->lib->cookie, ''); 
		if (stristr($page,">Extend traffic<")) 
			$this->error("LimitAcc");
		else if (stristr($page, "Hybrid-Traffic is completely exhausted")) 
			$this->error("LimitAcc");
		else if (stristr($page, "Our service is currently unavailable in your country")) 
			$this->error("blockCountry", true, false);
		else if (stristr($page, "You used too many different IPs")) 
			$this->error("blockAcc", true, false);
		else if (stristr($page, "Download Blocked (ip)")) 
			$this->error("blockIP", true, false);
		else if (!$this->isredirect($page)) {
			if (preg_match('/action="(https?:\/\/.+)" style/i', $page, $match))	
				return trim($match[1]);
		}
		else {
			if (stristr($this->redirect, "uploaded.net/404")) 
				$this->error("dead", true, false, 2);
			else 
				return trim($this->redirect);
		}
		return false;
	}

}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Uploaded Download Plugin 
* Downloader Class By [FZ]
* Fixed By djkristoph
* Fixed download link By giaythuytinh176 [5.8.2013]
* Fixed plugin by Steam [09, Feb 2014]
*/
?>
<?php
class dl_datafile_com extends Download {
	public function CheckAcc($cookie) {
		if (!stristr($cookie, "lang=en")) {
			$cookie = "lang=en; " . $cookie;
			$this->lib->save_cookies($this->site, $cookie);
		}
		$page = $this->lib->curl("https://www.datafile.com/profile.html", $cookie, '');
		if (stristr($page, ">Premium Expires:<")) 
			 return array(true, "Until " . $this->lib->cut_str($page, '<td class="el" >',  '&nbsp; ('));
		else if (stristr($page, '">Upgrade</a></span>)')) 
			return array(false, "accfree"); 
		else 
			return array(false, "accinvalid"); 
	}
	public function Login($user, $pass) {
		$page = $this->lib->curl("https://www.datafile.com/login.html", "lang=en", "login={$user}&password={$pass}&remember_me=1");
		$cookie = "lang=en; " . $this->lib->GetCookies($page);
		return $cookie;
	}
	public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		if ($pass)
			$this->error("linkpass", true, false, 2);
		$page = $this->lib->curl($url, $this->lib->cookie, '');
		if (stristr($page, "ErrorCode 0: Invalid Link")) 
			$this->error("dead", true, false, 2); 
		else if (preg_match('/https?:\/\/www?\.datafile\.com\/d\/([a-zA-z0-9]+)/', $url, $matches)) {
			$page = $this->lib->curl("http://www.datafile.com/files/download/file.html?code={$matches[1]}", $this->lib->cookie, '');
			if (stristr($page, "ErrorCode 6: Download limit in")) 
				$this->error("LimitAcc", true, false);
			else if ($this->isredirect($page)) {
				$redir = trim($this->redirect); 
				$name = $this->lib->getname($redir, $this->lib->cookie);
				$tach = explode(';', $name);
				$this->lib->reserved['filename'] = $tach[0];
				return $redir;
			}
		}
		return false;
	}
	
}
/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* DataFile.com Download Plugin by giaythuytinh176
* Downloader Class By [FZ]
* Date: 20.7.2013
* Fix check account by giaythuytinh176 [21.7.2013]
* Fix check account by giaythuytinh176 [6.8.2013]
*/
?>
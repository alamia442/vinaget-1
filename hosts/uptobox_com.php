<?php
class dl_uptobox_com extends Download {
    public function CheckAcc($cookie) {
        $page = $this->lib->curl("https://uptobox.com/?op=my_account", "lang=english;{$cookie}", '');
        if (stristr($page, "Premium account expiration date")) 
        	return array(true, "Until " . $this->lib->cut_str($page, "ation-date red'>", '</'));
        else if (stristr($page, "My affiliate link:") && !stristr($page, "Premium-Account expire")) 
        	return array(false, "accfree");
		else 
			return array(false, "accinvalid");
    }
    public function Login($user, $pass) {
    	$page   = $this->lib->curl("https://uptobox.com/?op=login", "lang=english", "login={$user}&password={$pass}");
        $cookie = "lang=english; {$this->lib->GetCookies($page)}";
		return $cookie;
    }
   	public function Leech($url) {
		list($url, $pass) = $this->linkpassword($url);
		$page = $this->lib->curl($url, $this->lib->cookie, '');
		if ($pass) {
			$post = $this->parseForm($this->lib->cut_str($page, "<form", "</form>"));
			$post["password"] = $pass;
			$page = $this->lib->curl($url, $this->lib->cookie, $post);
			if (stristr($page, "Wrong password"))  
				$this->error("wrongpass", true, false, 2);
			else if (preg_match('@https?:\/\/www\d+\.uptobox.com\/d\/[^\'\"\s\t<>\r\n]+@i', $page, $match)) 
				return trim(str_replace('https', 'http', $match[0]));
		}
		if (stristr($page,'type="password" name="password'))  
			$this->error("reportpass", true, false);
		else if (stristr($page, "The file was deleted by its owner") || stristr($page, "Page not found / La page")) 
			$this->error("dead", true, false, 2);
		else if (!$this->isredirect($page)) {
			$post = $this->parseForm($this->lib->cut_str($page, '<form name="F1"', "</form>"));
			$page = $this->lib->curl($url, $this->lib->cookie, $post);
			if (preg_match('@https?:\/\/www\d+\.uptobox.com\/d\/[^\'\"\s\t<>\r\n]+@i', $page, $match)) 
				return trim(str_replace('https', 'http', $match[0]));		
		}
		else 
			return trim(str_replace('https', 'http', trim($this->redirect))); 
		return false;
    }
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* Uptobox Download Plugin
* Downloader Class By [FZ]
* Support file password by giaythuytinh176 [26.7.2013][18.9.2013][Fixed]
* Fix by Rayyan2005
* Fix By Sky™ [10/02/2016]
*/
?>
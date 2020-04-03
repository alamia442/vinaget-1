<?php	
class dl_yunfile_com extends Download {
    public function CheckAcc($cookie) {
        $page = $this->lib->curl("http://www.yunfile.com/user/edit.html", $cookie, '');
        if (stristr($page, "you need to login in order to continue the operation!"))
            return array(false, "accinvalid");
        else if (stristr($page, "premium-pack") && stristr($page, "Expire")) 
            return array(true, "Until " . $this->lib->cut_str($page, '(Expire:',')'));
        else if (stristr($page, "premium-pack")) 
            return array(false, "accfree");
    }
    public function Login($user, $pass) {
        $page = $this->lib->curl("http://www.yunfile.com/view", "language=en_us", "module=member&action=validateLogin&username={$user}&password={$pass}&LoginButton=Login");
        $cookie = "language=en_us; {$this->lib->GetCookies($page)}";
		return $cookie;
    }
    public function Leech($url) {
        $url = preg_replace("@https?:\/\/(yfdisk|filemarkets|yunfile|tadown)\.com@", "http://page2.dfpan.com", $url);
        if ($this->lib->solveCaptcha != '') {
            $page = $this->lib->curl($url, $this->lib->cookie, '');
            if (stristr($page, "Please enter the verify code") || stristr($page, 'setCookie("vid1", "aaaaaaaaaaaaaaaa"')) {
                $id = $this->lib->cut_str($page, 'window.location = "', '"');
                $page = $this->lib->curl("http://page2.dfpan.com" . $id . $this->lib->solveCaptcha, $this->lib->cookie . "; vid1=aaaaaaaaaaaaaaaa;", '', 1, 0, $url);
                if (stristr($page, "Please enter the verify code") || stristr($page, 'setCookie("vid1", "aaaaaaaaaaaaaaaa"')) {
                    $this->lib->captcha = true;
                    return $url;
                }
                else if (preg_match('/href="(https?:\/\/.*\/downfile\/.*)" onclick="/i', $page, $matches)) {
                    return trim($matches[1]);
                }
            }
            else {
                $page = $this->lib->curl($url, $this->lib->cookie, '');
                if (preg_match('/href="(https?:\/\/.*\/downfile\/.*)" onclick="/i', $page, $matches)) {
                    return trim($matches[1]);
                }
            }
        }
        else {
            $page = $this->lib->curl($url, $this->lib->cookie, '');
            if (stristr($page, "Not found") || stristr($page, "Been deleted"))
                $this->error("dead", true, false, 2);
            else if (stristr($page, "Please enter the verify code") || stristr($page, 'setCookie("vid1", "aaaaaaaaaaaaaaaa"')) {
                $this->lib->captcha = true;
                return $url;
            }
            else if (preg_match('/href="(https?:\/\/.*\/downfile\/.*)" onclick="/i', $page, $matches)) {
                return trim($matches[1]);
            }
        }
		return false;
    }
    public function Captcha($hash) {
        $this->SaveImage("captcha/{$hash}.jpg", "http://page2.dfpan.com/verifyimg/getPcv.html");
        $urlCaptcha = str_replace("index.php", '', $this->lib->self) . "captcha/{$hash}.jpg";
        echo '<form method="GET"><font style="color: red; font-weight: bold;">Solve captcha to download link</span><br><img src="' . $urlCaptcha . '" style="margin: 2px 0 2px 0;" /><br><input name="download" style="display: none;" value="' . $hash . '" /><input name="captcha" style="width: 100px;" />&nbsp;<button type="submit">Download</button></form>';
    }
	public function GetInfo($url) {
        $url = preg_replace("@https?:\/\/(yfdisk|filemarkets|yunfile|tadown)\.com@", "http://page2.dfpan.com", $url);
        $page = $this->lib->curl($url, '', '', 0);
        $key = $this->lib->cut_str($page, 'codeAndEncode("', '"');
        $filename = $this->lib->cut_str($page, '<span id="file_show_filename">', '</span>');
        $filesize = $this->lib->cut_str($page, $filename . '</span> - ', ' </h2>');
        $filename = $this->DecodeName($key, $filename);
        return array($filesize, $filename);
    }
    private function CharCodeAt($str, $index) {
        $char = mb_substr($str, $index, 1, 'UTF-8');
        if (mb_check_encoding($char, 'UTF-8')) {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
            return hexdec(bin2hex($ret));
        } else 
            return null;
    }
    private function DecodeName($key, $str) {
        $keyUnicode = 0; $_str = "";
        for ($i = 0; $i < strlen($key); $i++) 
            $keyUnicode += $this->CharCodeAt($key, $i);
        for ($i = 0; $i < strlen($str); $i++) {
            $strXOR = $this->CharCodeAt($str, $i) ^ $keyUnicode;
            if (($strXOR == 147) || ($strXOR == 179))
                continue;
            $_str .= chr($strXOR);
        }  
        return $_str;
    }
    private function SaveImage($dir, $url) {
        $image = fopen($url, "rb");
        $file = fopen($dir, "wb");
        while ($chunk = fread($image, 8192))
            fwrite($file, $chunk, 8192);
        fclose($image); fclose($file);
        return false;
    }
}

/*
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* yunfile Download Plugin by giaythuytinh176 [13.8.2013]
* Downloader Class By [FZ]
*/
?>
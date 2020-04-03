<?php
class dl_4share_vn extends Download {  
    public function CheckAcc($cookie) {
        $page = $this->lib->curl("https://4share.vn/member", $cookie, '');
        if (stristr($page, "còn <b>0</b> ngày sử dụng")) 
            return array(true, "accfree");
        else if (stristr($page, ">Ngày hết hạn: <")) 
            return array(true, "Until: " . $this->lib->cut_str($page, "Ngày hết hạn: <b>", "</b> (còn") . "<br/> Traffic avaiable: " . $this->lib->cut_str($page, ">Bạn còn được download <strong>", "</strong>") . '/' . $this->lib->cut_str($page, "/Tổng số: <strong>", "</strong>"));
        else 
            return array(false, 'accinvalid');
    } 
    public function Login($user, $pass) {
        $page = $this->lib->curl("https://4share.vn/index/login", '', "username={$user}&password={$pass}");
        $cookie = $this->lib->GetCookies($page);
        return $cookie;
    }
    public function Leech($url) {
        $url = preg_replace("@https?:\/\/(up\.4share|4share)\.vn@", "https://4share.vn", $url);
        list($url, $pass) = $this->linkpassword($url); 
        $page = $this->lib->curl($url, $this->lib->cookie, '');
        if ($pass) 
            $page = $this->lib->curl($url, $this->lib->cookie, "password_download_input={$pass}");
        if (stristr($page, 'Bạn đợi ít phút để download file này!')) 
            $this->error('Bạn đợi ít phút để download file này!', true, false);
        else if (stristr($page, 'File is deleted?') || stristr($page,'File không tồn tại?')) 
            $this->error('File is deleted? (' .$this->lib->cut_str($page, 'File is deleted? (', ')<'). ')', true, false, 2);
        else if(stristr($page,"File này có password, bạn nãy nhập password để download"))    
            $this->error("reportpass", true, false);
        else if (preg_match('@https?:\/\/sv\d+\.4share\.vn\/\d+\/\?info=[^\'\r\n]+@i', $page, $dlink))
            return trim($dlink[0]);
        return false;
    }
 
}
 
/*
http://up.4share.vn/f/2516101316111016|chickenlam
* Open Source Project
* Vinaget by ..::[H]::..
* Version: 2.7.0
* 4Share.VN Download Plugin 
* Downloader Class By [FZ]
* Plugin By giaythuytinh176
* Date: 16.7.2013 
* Check account included - 18.7
* Fix login 4share [21.7.2013]
* Support file password by giaythuytinh176 [29.7.2013]
* Fixed check account by giaythuytinh176 [29.7.2013]
*/
?>
<?php
class dl_prembox_com extends Download {
    private function CreateToken() {
        $random = substr(mt_rand() / (mt_getrandmax() + 1), 2) . rand(1000, 10000);
        $token = 'dk$jfh149' . $random . $random{6} . 'fQ9YG:B%JuYj!$-6f8HEWw5@*NRBYx*_E@L';
        return $token;
    }

    private function Curl($action, $cookie, $params) {
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://prembox.com/papi" . $action);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:'
        ));
    
        $head[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
        $head[] = "Connection: keep-alive";
        $head[] = "Accept: */*";
        $head[] = "Accept-Encoding: gzip, deflate, br";
        $head[] = "Accept-Language: en-US,en;q=0.9";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_COOKIE, "utype=usr; " . $cookie);    
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params . "&app_token=" . $this->CreateToken());
    
        $page = curl_exec($ch);
        curl_close($ch);
    
        return $page;
    }
    
    public function CheckAcc($cookie) {
        list($_cookie, $token) = explode('; ', $cookie);
        $page = $this->Curl("/info", "ulogin={$_cookie}", "access_token={$token}");
        
        $json = @json_decode($page, true);
        if ($json['success'])  {
            if ($json['data']['accountType'] == "free")
                return array(false, "accfree");
            else
                return array(true, "Alo");
        }
        else
            return array(false, "accinvalid");
    }

    public function Login($user, $pass) {
        $page = $this->Curl("/login", '', "login={$user}&pass={$pass}");
        $json = @json_decode($page, true);
        
        $cookie = '';
        if ($json['success'])
            $cookie = $json['data']['access_cookie'] . '; ' . $json['data']['access_token'];
        
        return $cookie;
    }

	public function Leech($url) {
        list($cookie, $token) = explode('; ', $this->lib->cookie);
        list($url, $pass) = $this->linkpassword($url);

        $page = $this->Curl("/addLinkAndDownload", "ulogin={$cookie}", "download_type=d&links={$url}&access_token={$token}");

        $json = @json_decode($page, true);
        if ($json['success']) 
            return trim($json['data']);
        else
            $this->error($json['error'], 0, 0, 1);
        return false;
	}
}
?>
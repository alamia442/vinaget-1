<?php
/*
 * Vinaget by LTT♥
 * Script Name: Vinaget
 * Version: 3.3 LTSB
 */

/* Set default timezone */
date_default_timezone_set('UTC');

// #################################### Begin class getinfo #####################################
class getinfo extends Tools_get
{
    public function config()
    {
        $this->self         = 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('/\?.*$/', '', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);
        $this->Deny         = true;
        $this->admin        = false;
        $this->fileinfo_dir = "data";
        $this->filecookie   = "/cookie.dat";
        $this->fileconfig   = "/config.dat";
        $this->fileaccount  = "/account.dat";
        $this->fileinfo_ext = "dat";
        $this->banned       = explode(' ', '.htaccess .htpasswd .php .php3 .php4 .php5 .phtml .asp .aspx .cgi .pl');
        $this->unit         = 512;
        $this->UserAgent    = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36';
        $this->config       = $this->load_json($this->fileconfig);
        include "config.php";
        if (count($this->config) == 0) {
            $this->config = $config;
            $_GET['id']   = 'admin';
            $this->Deny   = false;
            $this->admin  = true;
        } else {
            foreach ($config as $key => $val) {
                if (!isset($this->config[$key])) {
                    $this->config[$key] = $val;
                }
            }
            if ($this->config['secure'] == false) {
                $this->Deny = false;
            }
            
            $password   = explode(", ", $this->config['password']);
            $password[] = $this->config['admin'];
            foreach ($password as $login_vng) {
                if (isset($_COOKIE["secureid"]) && $_COOKIE["secureid"] == md5($login_vng)) {
                    $this->Deny = false;
                    break;
                }
            }
            
        }
        $this->set_config();
        if (!file_exists($this->fileinfo_dir)) {
            mkdir($this->fileinfo_dir) or die("<CENTER><font color=red size=4>Could not create folder! Try to chmod the folder \"<B>$this->fileinfo_dir</B>\" to 777</font></CENTER>");
            @chmod($this->fileinfo_dir, 0777);
        }
        if (!file_exists($this->fileinfo_dir . "/files")) {
            mkdir($this->fileinfo_dir . "/files") or die("<CENTER><font color=red size=4>Could not create folder! Try to chmod the folder \"<B>$this->fileinfo_dir/files</B>\" to 777</font></CENTER>");
            @chmod($this->fileinfo_dir . "/files", 0777);
        }
        if (!file_exists($this->fileinfo_dir . "/index.php")) {
            $clog = fopen($this->fileinfo_dir . "/index.php", "a") or die("<CENTER><font color=red size=4>Could not create folder! Try to chmod the folder \"<B>$this->fileinfo_dir</B>\" to 777</font></CENTER>");
            fwrite($clog, '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=http://' . $homepage . '">');
            fclose($clog);
            @chmod($this->fileinfo_dir . "/index.php", 0666);
        }
        if (!file_exists($this->fileinfo_dir . "/files/index.php")) {
            $clog = fopen($this->fileinfo_dir . "/files/index.php", "a") or die("<CENTER><font color=red size=4>Could not create folder! Try to chmod the folder \"<B>$this->fileinfo_dir/files</B>\" to 777</font></CENTER>");
            fwrite($clog, '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=http://' . $homepage . '">');
            fclose($clog);
            @chmod($this->fileinfo_dir . "/files/index.php", 0666);
        }
    }
    
    public function set_config()
    {
        include "lang/{$this->config['language']}.php";
        $this->lang             = $lang;
        $this->secure           = $this->config['secure'];
        $this->hide_page        = $this->config['hide_page'];
        $this->keyword_page     = $this->config['keyword_page'];
        $this->skin             = $this->config['skin'];
        $this->download_prefix  = $this->config['download_prefix'];
        $this->download_suffix  = $this->config['download_suffix'];
        $this->limitMBIP        = $this->config['limitMBIP'];
        $this->ttl              = $this->config['ttl'];
        $this->limitPERIP       = $this->config['limitPERIP'];
        $this->ttl_ip           = $this->config['ttl_ip'];
        $this->max_jobs_per_ip  = $this->config['max_jobs_per_ip'];
        $this->max_jobs         = $this->config['max_jobs'];
        $this->max_load         = $this->config['max_load'];
        $this->max_size_default = $this->config['max_size_default'];
        $this->file_size_limit  = $this->config['file_size_limit'];
        $this->adslink          = $this->config['adslink'];
        $this->api_ads          = $this->config['api_ads'];
        $this->tinyurl          = $this->config['tinyurl'];
        $this->badword          = explode(", ", $this->config['badword']);
        $this->act              = array(
            'rename' => $this->config['rename'],
            'delete' => $this->config['delete']
        );
        $this->listfile         = $this->config['listfile'];
        $this->showlinkdown     = $this->config['showlinkdown'];
        $this->checkacc         = $this->config['checkacc'];
        $this->privatef         = $this->config['privatefile'];
        $this->privateip        = $this->config['privateip'];
        $this->redirdl          = $this->config['redirectdl'];
        $this->check3x          = $this->config['checklinksex'];
        $this->colorfn          = $this->config['colorfilename'];
        $this->colorfs          = $this->config['colorfilesize'];
        $this->title            = $this->config['title'];
        $this->directdl         = $this->config['showdirect'];
        $this->display_error    = $this->config['display_error'];
        $this->proxy            = false;
        $this->bbcode           = $this->config['bbcode'];
        $this->hide_plugins_col = $this->config['hide_plugins_col'];
        $this->hide_preacc_col  = $this->config['hide_preacc_col'];
        $this->hide_number_acc  = $this->config['hide_number_acc'];
        $this->del_checked_acc  = $this->config['del_checked_acc'];
        $this->debrid_mode      = $this->config['debrid_mode'];
        $this->debrid_host      = $this->config['debrid_host'];   
        $this->prox             = isset($_POST['proxy']) ? $_POST['proxy'] : '';
    }
    
    public function isadmin()
    {
        return (isset($_COOKIE['secureid']) && $_COOKIE['secureid'] == md5($this->config['admin']) ? true : $this->admin);
    }
    
    public function notice($id = "notice")
    {
        if ($id == "notice") {
            return sprintf($this->lang['notice'], Tools_get::convert_time($this->ttl * 60), $this->limitPERIP, Tools_get::convert_time($this->ttl_ip * 60));
        } else {
            $this->CheckMBIP();
            $MB1IP         = Tools_get::convertmb($this->countMBIP * 1024 * 1024);
            $thislimitMBIP = Tools_get::convertmb($this->limitMBIP * 1024 * 1024);
            $maxsize       = Tools_get::convertmb($this->max_size_other_host * 1024 * 1024);
            if ($id == "yourip") {
                return $this->lang['yourip'];
            }
            
            if ($id == "yourjob") {
                return $this->lang['yourjob'];
            }
            
            if ($id == "userjobs") {
                return ' ' . $this->lookup_ip($_SERVER['REMOTE_ADDR']) . ' (max ' . $this->max_jobs_per_ip . ') ';
            }
            
            if ($id == "youused") {
                return sprintf($this->lang['youused']);
            }
            
            if ($id == "used") {
                return ' ' . $MB1IP . ' (max ' . $thislimitMBIP . ') ';
            }
            
            if ($id == "sizelimit") {
                return $this->lang['sizelimit'];
            }
            
            if ($id == "maxsize") {
                return $maxsize;
            }
            
            if ($id == "totjob") {
                return $this->lang['totjob'];
            }
            
            if ($id == "totjobs") {
                return ' ' . count($this->jobs) . ' (max ' . $this->max_jobs . ') ';
            }
            
            if ($id == "serverload") {
                return $this->lang['serverload'];
            }
            
            if ($id == "maxload") {
                return ' ' . $this->get_load() . ' (max ' . $this->max_load . ') ';
            }
            
            if ($id == "uonline") {
                return $this->lang['uonline'];
            }
            
            if ($id == "useronline") {
                return Tools_get::useronline();
            }
            
        }
    }
    
    public function load_jobs()
    {
        if (isset($this->jobs)) {
            return;
        }
        
        $dir         = opendir($this->fileinfo_dir . "/files/");
        $this->lists = array();
        while ($file = readdir($dir)) {
            if (substr($file, -strlen($this->fileinfo_ext) - 1) == "." . $this->fileinfo_ext) {
                $this->lists[] = $this->fileinfo_dir . "/files/" . $file;
            }
        }
        closedir($dir);
        $this->jobs = array();
        if (count($this->lists)) {
            sort($this->lists);
            foreach ($this->lists as $file) {
                $contentsfile = @file_get_contents($file);
                $jobs_data    = @json_decode($contentsfile, true);
                if (is_array($jobs_data)) {
                    $this->jobs = array_merge($this->jobs, $jobs_data);
                }
            }
        }
    }
    
    public function save_jobs()
    {
        if (!isset($this->jobs) || is_array($this->jobs) == false) {
            return;
        }
        
        // ## clean jobs ###
        $oldest = time() - $this->ttl * 60;
        $delete = array();
        foreach ($this->jobs as $key => $job) {
            if ($job['mtime'] < $oldest) {
                $delete[] = $key;
            }
        }
        foreach ($delete as $key) {
            unset($this->jobs[$key]);
        }
        // ## clean jobs ###
        $namedata       = $timeload = explode(" ", microtime());
        $namedata       = $namedata[1] * 1000 + round($namedata[0] * 1000);
        $this->fileinfo = $this->fileinfo_dir . "/files/" . $namedata . "." . $this->fileinfo_ext;
        $tmp            = @json_encode($this->jobs);
        $fh = fopen($this->fileinfo, 'w') or die('<CENTER><font color=red size=3>Could not open file ! Try to chmod the folder "<B>' . $this->fileinfo_dir . "/files/" . '</B>" to 777</font></CENTER>');
        fwrite($fh, $tmp);
        fclose($fh);
        @chmod($this->fileinfo, 0666);
        if (count($this->lists)) {
            foreach ($this->lists as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        
        return true;
    }
    
    public function load_json($file)
    {
        $hash              = substr($file, 1);
        $this->json[$hash] = @file_get_contents($this->fileinfo_dir . $file);
        $data              = @json_decode($this->json[$hash], true);
        if (!is_array($data)) {
            $data              = array();
            $this->json[$hash] = 'default';
        }
        return $data;
    }
    
    public function save_json($file, $data)
    {
        $tmp  = json_encode($data);
        $hash = substr($file, 1);
        if ($tmp !== $this->json[$hash]) {
            $this->json[$hash] = $tmp;
            $fh = fopen($this->fileinfo_dir . $file, 'w') or die('<CENTER><font color=red size=3>Could not open file ! Try to chmod the folder "<B>' . $this->fileinfo_dir . '</B>" to 777</font></CENTER>');
            fwrite($fh, $this->json[$hash]) or die('<CENTER><font color=red size=3>Could not write file ! Try to chmod the folder "<B>' . $this->fileinfo_dir . '</B>" to 777</font></CENTER>');
            fclose($fh);
            @chmod($this->fileinfo_dir . $file, 0666);
            return true;
        }
    }
    
    public function load_cookies()
    {
        if (isset($this->cookies)) {
            return;
        }
        
        $this->cookies = $this->load_json($this->filecookie);
    }
    
    public function get_cookie($site)
    {
        $cookie = "";
        if (isset($this->cookies) && count($this->cookies) > 0) {
            foreach ($this->cookies as $ckey => $cookies) {
                if ($ckey === $site) {
                    $cookie = $cookies['cookie'];
                    break;
                }
            }
        }
        return $cookie;
    }
    
    public function save_cookies($site, $cookie)
    {
        if (!isset($this->cookies)) {
            return;
        }
        
        if ($site) {
            $cookies = array(
                'cookie' => $cookie,
                'time' => time()
            );
            $this->cookies[$site] = $cookies;
        }
        
        $this->save_json($this->filecookie, $this->cookies);
    }
    
    public function load_account()
    {
        if (isset($this->acc)) {
            return;
        }
        
        $this->acc = $this->load_json($this->fileaccount);
        foreach ($this->list_host as $site => $host) {
            if (!$host['alias']) {
                if (empty($this->acc[$site]['proxy'])) {
                    $this->acc[$site]['proxy'] = "";
                }
                
                if (empty($this->acc[$site]['direct'])) {
                    $this->acc[$site]['direct'] = false;
                }
                
                if (empty($this->acc[$site]['max_size'])) {
                    $this->acc[$site]['max_size'] = $this->max_size_default;
                }
                
                if (empty($this->acc[$site]['accounts'])) {
                    $this->acc[$site]['accounts'] = array();
                }
                
            }
        }
        foreach ($this->list_host_debrid as $site => $host) {
            if (!$host['alias']) {
                if (empty($this->acc[$site]['proxy'])) {
                    $this->acc[$site]['proxy'] = "";
                }
                
                if (empty($this->acc[$site]['direct'])) {
                    $this->acc[$site]['direct'] = false;
                }
                
                if (empty($this->acc[$site]['max_size'])) {
                    $this->acc[$site]['max_size'] = $this->max_size_default;
                }
                
                if (empty($this->acc[$site]['accounts'])) {
                    $this->acc[$site]['accounts'] = array();
                }
                
            }
        }        
    }
    
    public function save_account($service, $acc)
    {
        foreach ($this->acc[$service]['accounts'] as $value) {
            if ($acc == $value) {
                return false;
            }
        }
        
        if (empty($this->acc[$service])) {
            $this->acc[$service]['max_size'] = $this->max_size_default;
        }
        
        $this->acc[$_POST['type']]['accounts'][] = $_POST['account'];
        $this->save_json($this->fileaccount, $this->acc);
    }
    
    public function get_account($service)
    {
        $acc = '';
        if (isset($this->acc[$service])) {
            $service        = $this->acc[$service];
            $this->max_size = $service['max_size'];
            if (count($service['accounts']) > 0) {
                $acc = $service['accounts'][rand(0, count($service['accounts']) - 1)];
            }
            
        }
        return $acc;
    }
    
    public function lookup_job($hash)
    {
        $this->load_jobs();
        foreach ($this->jobs as $key => $job) {
            if ($job['hash'] === $hash) {
                return $job;
            }
            
        }
        return false;
    }
    
    public function check_jobs()
    {
        $ip       = $_SERVER['REMOTE_ADDR'];
        $heute    = 0;
        $lasttime = time();
        $altr     = $lasttime - $this->ttl_ip * 60;
        foreach ($this->jobs as $job) {
            if ($job['ip'] === $ip && $job['mtime'] > $altr) {
                $heute++;
                if ($job['mtime'] < $lasttime) {
                    $lasttime = $job['mtime'];
                }
                
            }
        }
        $lefttime = $this->ttl_ip * 60 - time() + $lasttime;
        $lefttime = $this->convert_time($lefttime);
        return array(
            $heute,
            $lefttime
        );
    }
    
    public function get_load($i = 0)
    {
        $load = array(
            '0',
            '0',
            '0'
        );
        if (@file_exists('/proc/loadavg')) {
            if ($fh = @fopen('/proc/loadavg', 'r')) {
                $data = @fread($fh, 15);
                @fclose($fh);
                $load = explode(' ', $data);
            }
        } else {
            if ($serverstats = @exec('uptime')) {
                if (preg_match('/(?:averages)?\: ([0-9\.]+),?[\s]+([0-9\.]+),?[\s]+([0-9\.]+)/', $serverstats, $matches)) {
                    $load = array(
                        $matches[1],
                        $matches[2],
                        $matches[3]
                    );
                }
            }
        }
        return $i == -1 ? $load : $load[$i];
    }
    
    public function lookup_ip($ip)
    {
        $this->load_jobs();
        $cnt = 0;
        foreach ($this->jobs as $job) {
            if ($job['ip'] === $ip) {
                $cnt++;
            }
            
        }
        return $cnt;
    }
}
// #################################### End class getinfo #######################################
// #################################### Begin class stream_get ##################################

class stream_get extends getinfo
{
    public function __construct()
    {
        $this->config();
        $this->load_jobs();
        $this->load_cookies();
        $this->cookie = '';
        $this->max_size_other_host = $this->file_size_limit;
        if (isset($_REQUEST['download'])) {
            $this->download($_REQUEST['download']);
        }
        else {
            include "hosts/hosts.php";
            ksort($host); ksort($debrid);
            $this->list_host = $host;
            $this->list_host_debrid = $debrid;
            $this->load_account();
        }
        if (isset($_COOKIE['owner'])) {
            $this->owner = $_COOKIE['owner'];
        } else {
            $this->owner = intval(rand() * 10000);
            setcookie('owner', $this->owner, 0);
        }
    }
    
    public function download($hash)
    {
        error_reporting(0);
        $job = $this->lookup_job($hash);
        if (!$job) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            die($this->lang['errorget']);
        }
        if (($_SERVER['REMOTE_ADDR'] !== $job['ip']) && $this->privateip == true) {
            sleep(15);
            die($this->lang['errordl']);
        }
        if ($this->get_load() > $this->max_load) {
            sleep(15);
        }
        
        $link         = '';
        $filesize     = $job['size'];
        $referer      = urldecode($job['url']);
        $filename     = $this->download_prefix . Tools_get::convert_name($job['filename']) . $this->download_suffix;
        $directlink   = urldecode($job['directlink']['url']);
        $this->cookie = $job['directlink']['cookies'];
        $link         = $directlink;
        $link         = str_replace(" ", "%20", $link);
        
        if (!$link) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            $this->error1('erroracc');
        }
        if ($job['proxy'] != 0 && $this->redirdl == true) {
            list($ip) = explode(":", $job['proxy']);
            if ($_SERVER['REMOTE_ADDR'] != $ip) {
                $this->wrong_proxy($job['proxy']);
            } else {
                header('Location: ' . $link);
                die;
            }
        }
        $range = '';
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = substr($_SERVER['HTTP_RANGE'], 6);
            list($start, $end) = explode('-', $range);
            $new_length = $filesize - $start;
        }
        $port   = 80;
        $schema = parse_url(trim($link));
        $host   = $schema['host'];
        $scheme = "http://";
        $gach   = explode("/", $link);
        list($path1, $path) = explode($gach[2], $link);
        if (isset($schema['port'])) {
            $port = $schema['port'];
        } elseif ($schema['scheme'] == 'https') {
            $scheme = "ssl://";
            $port   = 443;
        }
        if ($scheme != "ssl://") {
            $scheme = "";
        }
        $hosts = $scheme . $host . ':' . $port;
        if ($job['proxy'] != 0) {
            if (strpos($job['proxy'], "|")) {
                list($ip, $user) = explode("|", $job['proxy']);
                $auth = base64_encode($user);
            } else {
                $ip = $job['proxy'];
            }
            
            $data = "GET {$path} HTTP/1.1\r\n";
            if (isset($auth)) {
                $data .= "Proxy-Authorization: Basic $auth\r\n";
            }
            
            $fp = @stream_socket_client("tcp://{$ip}", $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        } else {
            $data = "GET {$path} HTTP/1.1\r\n";
            $fp   = @stream_socket_client($hosts, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        }
        if (!$fp) {
            sleep(15);
            header("HTTP/1.1 404 Not Found");
            die("HTTP/1.1 404 Not Found");
        }
        $data .= "User-Agent: " . $this->UserAgent . "\r\n";
        $data .= "Host: {$host}\r\n";
        $data .= "Accept: */*\r\n";
        $data .= "Referer: {$referer}\r\n";
        $data .= $this->cookie ? "Cookie: " . $this->cookie . "\r\n" : '';
        if (!empty($range)) {
            $data .= "Range: bytes={$range}\r\n";
        }
        
        $data .= "Connection: Close\r\n\r\n";
        @stream_set_timeout($fp, 2);
        fputs($fp, $data);
        fflush($fp);
        $header = '';
        $header .= stream_get_line($fp, $this->unit);
        
        do {
            if (!$header) {
                $header .= stream_get_line($fp, $this->unit);
                print_r($header);
                if (!stristr($header, "HTTP/1")) {
                    break;
                }
                
            } else {
                $header .= stream_get_line($fp, $this->unit);
            }
            
        } while (stristr($header, "\r\n") == false);

        // Must be fresh start
        if (headers_sent()) {
            die('Headers Sent');
        }
        
        // Required for some browsers
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        
        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        
        if (stristr($header, "TTP/1.0 200 OK") || stristr($header, "TTP/1.1 200 OK")) {
            if (!is_numeric($filesize)) {
                $filesize = trim($this->cut_str($header, "Content-Length:", "\n"));
            }
            
            if (stristr($header, "filename") && !$filename) {
                $filename = trim($this->cut_str($header, "filename", "\n"));
                $filename = preg_replace("/(\"\;\?\=|\"|=|\*|UTF-8|\')/U", "", $filename);
                $filename = $this->download_prefix . $filename . $this->download_suffix;
            }
            if (is_numeric($filesize)) {
                header("HTTP/1.1 200 OK");
                header("Content-Type: application/force-download");
                header("Content-Disposition: attachment; filename=\"" . str_replace(" ", "_", $filename) . "\"");
                header("Content-Length: {$filesize}");
            } else {
                sleep(5);
                header("HTTP/1.1 404 Not Found");
                die("HTTP/1.1 404 Not Found");
            }
        } elseif (stristr($header, "TTP/1.1 206") || stristr($header, "TTP/1.0 206")) {
            sleep(2);
            header("HTTP/1.1 206 Partial Content");
            header("Content-Type: application/force-download");
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range/{$filesize}");
        } else {
            sleep(10);
            header("HTTP/1.1 404 Not Found");
            die("HTTP/1.1 404 Not Found");
        }
        $tmp = explode("\r\n\r\n", $header);
        $max = count($tmp);
        for ($i = 1; $i < $max; $i++) {
            print $tmp[$i];
            if ($i != $max - 1) {
                echo "\r\n\r\n";
            }
            
        }
        while (!feof($fp) && (connection_status() == 0)) {
            $recv = @stream_get_line($fp, $this->unit);
            @print $recv;
            @flush();
            @ob_flush();
        }
        fclose($fp);
        exit;
    }
    
    public function CheckMBIP()
    {
        $this->countMBIP = 0;
        $this->totalMB   = 0;
        $this->timebw    = 0;
        $timedata        = time();
        foreach ($this->jobs as $job) {
            if ($job['ip'] == $_SERVER['REMOTE_ADDR']) {
                $this->countMBIP = $this->countMBIP + $job['size'] / 1024 / 1024;
                if ($job['mtime'] < $timedata) {
                    $timedata = $job['mtime'];
                }
                
                $this->timebw = $this->ttl * 60 + $timedata - time();
            }
            
            if ($this->privatef == false) {
                $this->totalMB = $this->totalMB + $job['size'] / 1024 / 1024;
                $this->totalMB = round($this->totalMB);
            } else {
                if ($job['owner'] == $this->owner) {
                    $this->totalMB = $this->totalMB + $job['size'] / 1024 / 1024;
                    $this->totalMB = round($this->totalMB);
                }
            }
        }
        
        $this->countMBIP = round($this->countMBIP);
        if ($this->countMBIP >= $this->limitMBIP) {
            return false;
        }
        
        return true;
    }
    
    public function curl($url, $cookies, $post, $header = 1, $json = 0, $ref = 0, $xml = 0)
    {
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($json == 1) {
            $head[] = "Content-type: application/json";
            $head[] = "X-Requested-With: XMLHttpRequest";
        }
        if ($xml == 1) {
            $head[] = "X-Requested-With: XMLHttpRequest";
        }
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $head[] = "Accept-Language: en-us,en;q=0.5";
        
        if ($cookies) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }
        
        curl_setopt($ch, CURLOPT_USERAGENT, $this->UserAgent);
        curl_setopt($ch, CURLOPT_REFERER, $ref === 0 ? $url : $ref);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        if ($header == -1) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        } else {
            curl_setopt($ch, CURLOPT_HEADER, $header);
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($this->proxy != false) {
            if (strpos($this->proxy, "|")) {
                list($ip, $auth) = explode("|", $this->proxy);
                curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $auth);
            } else {
                $ip = $this->proxy;
            }
            
            curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
            curl_setopt($ch, CURLOPT_PROXY, $ip);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:'
        ));
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
    
    public function cut_str($str, $left, $right)
    {
        $str     = substr(stristr($str, $left), strlen($left));
        $leftLen = strlen(stristr($str, $right));
        $leftLen = $leftLen ? -($leftLen) : strlen($str);
        $str     = substr($str, 0, $leftLen);
        return $str;
    }
    
    public function GetCookies($content)
    {
        preg_match_all('/Set-Cookie: (.*); /Ui', $content, $temp);
        $cookie  = $temp[1];
        $cookies = "";
        $a       = array();
        foreach ($cookie as $c) {
            $pos     = strpos($c, "=");
            $key     = substr($c, 0, $pos);
            $val     = substr($c, $pos + 1);
            $a[$key] = $val;
        }
        foreach ($a as $b => $c) {
            $cookies .= "{$b}={$c}; ";
        }
        return $cookies;
    }
    
    public function GetAllCookies($page)
    {
        $lines     = explode("\n", $page);
        $retCookie = "";
        foreach ($lines as $val) {
            preg_match('/Set-Cookie: (.*)/i', $val, $temp);
            if (isset($temp[1])) {
                if ($cook = substr($temp[1], 0, stripos($temp[1], ';'))) {
                    $retCookie .= $cook . ";";
                }
                
            }
        }
        
        return $retCookie;
    }
    
    public function mf_str_conv($str_or)
    {
        $str_or = stripslashes($str_or);
        if (!preg_match("/unescape\(\W([0-9a-f]+)\W\);\w+=([0-9]+);[^\^]+\)([0-9\^]+)?\)\);eval/", $str_or, $match)) {
            return $str_or;
        }
        
        $match[3] = $match[3] ? $match[3] : "";
        $str_re   = "";
        for ($i = 0; $i < $match[2]; $i++) {
            $c = HexDec(substr($match[1], $i * 2, 2));
            eval("\$c = \$c" . $match[3] . ";");
            $str_re .= chr($c);
        }
        
        $str_re = str_replace($match[0], stripslashes($str_re), $str_or);
        if (preg_match("/unescape\(\W([0-9a-f]+)\W\);\w+=([0-9]+);[^\^]+\)([0-9\^]+)?\)\);eval/", $str_re, $dummy)) {
            $str_re = $this->mf_str_conv($str_re);
        }
        
        return $str_re;
    }
    
    public function main()
    {
        if ($this->get_load() > $this->max_load) {
            echo '<center><b><i><font color=red>' . $this->lang['svload'] . '</font></i></b></center>';
            return;
        }
        
        if (isset($_POST['urllist'])) {
            $url = $_POST['urllist'];
            $url = str_replace("\r", "", $url);
            $url = str_replace("\n", "", $url);
            $url = str_replace("<", "", $url);
            $url = str_replace(">", "", $url);
            $url = str_replace(" ", "", $url);
        }
        
        if (isset($url) && strlen($url) > 10) {
            if (substr($url, 0, 4) == 'www.') {
                $url = "http://" . $url;
            }
            
            if (!$this->check3x)
                $dlhtml = $this->get($url); 
            else {
                // ################## CHECK 3X #########################
                
                $check3x = false;
                if (strpos($url, "|not3x")) {
                    $url = str_replace("|not3x", "", $url);
                } else {
                    $data = strtolower($this->google($url));
                    if (strlen($data) > 1) {
                        foreach ($this->badword as $bad) {
                            if (stristr($data, " {$bad}") || stristr($data, "_{$bad}") || stristr($data, ".{$bad}") || stristr($data, "-{$bad}")) {
                                $check3x = $bad;
                                break;
                            }
                        }
                    }
                }
                
                if ($check3x == false)
                    $dlhtml = $this->get($url);
                    
                else {
                    $dlhtml = printf($this->lang['issex'], $url);
                    unset($check3x);
                }
                // ################## CHECK 3X #########################
                
            }
        } else {
            $dlhtml = "<b><a href=" . $url . " style='TEXT-DECORATION: none'><font color=red face=Arial size=2><s>" . $url . "</s></font></a> <img src=images/chk_error.png width='15' alt='errorlink'> <font color=#ffcc33><B>" . $this->lang['errorlink'] . "</B></font><br />";
        }
        
        echo $dlhtml;
    }
    
    public function google($q)
    {
        $q               = urldecode($q);
        $q               = str_replace(' ', '+', $q);
        $oldagent        = $this->UserAgent;
        $this->UserAgent = "Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; NOKIA; Lumia 800)";
        $data            = $this->curl("http://www.google.com/search?q={$q}&hl=en", '', '', 0);
        $this->UserAgent = $oldagent;
        $parsing         = $this->cut_str($data, '<ol>', '</ol>');
        $new             = "<ol>{$parsing}</ol>";
        $new             = str_replace('<ol><li class="g">', "", $new);
        $new             = str_replace('</li><li class="g">', "\n\n\n", $new);
        $new             = str_replace('</li></ol>', "", $new);
        $new             = preg_replace('%<a(.*?)href[^<>]+>|</a>%s', "", $new);
        $new             = preg_replace('%<b>|</b>%s', "", $new);
        $new             = preg_replace('%<h3 class="r">|</h3>%s', "", $new);
        $new             = preg_replace('%<div class="s"><div class="kv" style="margin-bottom:2px"><cite>[^<]+</cite></div><span class="st">%s', " ", $new);
        $new             = str_replace(' ...', "", $new);
        $new             = strip_tags($new);
        $new             = str_replace('â€Ž', '', $new);
        $new             = str_replace('', '', $new);
        $new             = htmlspecialchars_decode($new);
        return $new;
    }
    
    public function getsize($link, $cookie = "")
    {
        $size_name = Tools_get::size_name($link, $cookie == "" ? $this->cookie : $cookie);
        return $size_name[0];
    }
    
    public function getname($link, $cookie = "")
    {
        $size_name = Tools_get::size_name($link, $cookie == "" ? $this->cookie : $cookie);
        return $size_name[1];
    }
    
    public function get($url)
    {
        $this->reserved = array();
        $this->CheckMBIP();
        /* Check */
        if (count($this->jobs) >= $this->max_jobs) 
            $this->error1('manyjob');
        if ($this->countMBIP >= $this->limitMBIP) 
            $this->error1('countMBIP', Tools_get::convertmb($this->limitMBIP * 1024 * 1024), Tools_get::convert_time($this->ttl * 60), Tools_get::convert_time($this->timebw));
        /* Check again */
        $checkjobs = $this->check_jobs();
        $heute     = $checkjobs[0];
        $lefttime  = $checkjobs[1];
        if ($heute >= $this->limitPERIP) 
            $this->error1('limitPERIP', $this->limitPERIP, Tools_get::convert_time($this->ttl_ip * 60), $lefttime);
        if ($this->lookup_ip($_SERVER['REMOTE_ADDR']) >= $this->max_jobs_per_ip)
            $this->error1('limitip');
        /* Check url */
        $url = trim($url);
        if (empty($url)) 
            return;
        /* Start */
        $Original = $url;
        $link     = '';
        $cookie   = '';
        $dlhtml   = '';
        if (!$this->debrid_mode) {
            $schema      = str_replace("www.", "", $this->cut_str($Original, "://", "/"));
            if (stristr($schema, "isra.cloud"))
                $domain = "isra.cloud";
            else {
                preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $schema, $matches);
                    $domain  = $matches['domain'];
            }
            $site        = $this->list_host[$domain]['site'];
            $this->proxy = (isset($this->prox) && $this->prox != '') ? $this->prox : (isset($this->acc[$site]['proxy']) ? $this->acc[$site]['proxy'] : false);
            $this->max_size = $this->acc[$site]['max_size'];
            for ($i = 1; $i <= 3; $i++) {
                if ($link)
                    break;
                if ($this->get_account($site) != "") { 
                    require_once 'hosts/' . $this->list_host[$site]['file'];
                    $download    = new $this->list_host[$site]['class']($this, $this->list_host[$site]['site']);
                    $link        = $download->General($url);        
                }
            }            
        }
        else {
            $schema      = str_replace("www.", "", $this->cut_str($Original, "://", "/"));
            if (stristr($schema, "isra.cloud"))
                $domain = "isra.cloud";
            else {
                preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $schema, $matches);
                    $domain  = $matches['domain'];
            }
            $site        = $this->list_host_debrid[$this->debrid_host]['site'];
            $this->proxy = (isset($this->prox) && $this->prox != '') ? $this->prox : (isset($this->acc[$site]['proxy']) ? $this->acc[$site]['proxy'] : false);
            $this->max_size = $this->acc[$site]['max_size'];
            for ($i = 1; $i <= 3; $i++) {
                if ($link)
                    break;
                if ($this->get_account($site) != "") { 
                    require_once 'hosts/debrid/' . $this->list_host_debrid[$site]['file'];
                    $download    = new $this->list_host_debrid[$site]['class']($this, $this->list_host_debrid[$site]['site']);
                    $link        = $download->General($url);   
                }
            }          
        }

        if (!$link) {
            $size_name      = Tools_get::size_name($Original, "");
            $filesize       = $size_name[0];
            $filename       = $size_name[1];
            if ($filesize > 1024 * 100) 
                $link = $url;
            else 
                $this->error2('notsupport', $Original);                
        } 
        else {
            $size_name = Tools_get::size_name($link, $this->cookie);
            $filesize  = $size_name[0];
            $filename  = isset($this->reserved['filename']) ? $this->reserved['filename'] : $size_name[1];
        }
        /* Check */
        if (!isset($filesize)) 
            $this->error2('notsupport', $Original);
        if (!isset($this->max_size)) 
            $this->max_size = $this->max_size_other_host;
        /* Convert & Check */
        $msize = Tools_get::convertmb($filesize);
        $hash  = md5($_SERVER['REMOTE_ADDR'] . $Original);
        if ($hash === false) 
            $this->error1('cantjob');
        if ($filesize > $this->max_size * 1024 * 1024) 
            $this->error2('filebig', $Original, $msize, Tools_get::convertmb($this->max_size * 1024 * 1024));
        if (($this->countMBIP + $filesize / (1024 * 1024)) >= $this->limitMBIP) 
            $this->error1('countMBIP', Tools_get::convertmb($this->limitMBIP * 1024 * 1024), Tools_get::convert_time($this->ttl * 60), Tools_get::convert_time($this->timebw));
        /* Check */
        $checkjobs = $this->check_jobs();
        $heute     = $checkjobs[0];
        $lefttime  = $checkjobs[1];
        if ($heute >= $this->limitPERIP) 
            $this->error1('limitPERIP', $this->limitPERIP, Tools_get::convert_time($this->ttl_ip * 60), $lefttime);
        /* Build job */
        $job = array(
            'hash' => substr(md5($hash), 0, 10),
            'path' => substr(md5(rand()), 0, 5),
            'filename' => urlencode($filename),
            'size' => $filesize,
            'msize' => $msize,
            'mtime' => time(),
            'speed' => 0,
            'url' => urlencode($Original),
            'host' => $domain,
            'owner' => $this->owner,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'type' => 'direct',
            'proxy' => $this->proxy == false ? 0 : $this->proxy,
            'directlink' => array(
                'url' => urlencode($link),
                'cookies' => $this->cookie
            )
        );
        /* Save job */
        $this->jobs[$hash] = $job;
        $this->save_jobs();
        $tiam    = time() . rand(0, 999);
        $gach    = explode('/', $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        if ($this->acc[$site]['direct']) 
            $linkdown = $link;
        else
            $linkdown = $this->self . '?download=' . $job['hash'];
        /* Short link */
        if (empty($linkdown) == false && $this->adslink == true && empty($this->api_ads) == false) {
            $linkdown = $this->api_zip($linkdown, $this->api_ads);
        }
        if (empty($linkdown) == false && $this->tinyurl == true) {
            $datalink = $this->tinyurl($linkdown);
            if (preg_match('%(http:\/\/.++)%U', $datalink, $shortlink)) {
                $linkdown = trim($shortlink[1]);
            }
        }
        /* Build BbCode */
        $lik = $linkdown;
        if ($this->bbcode) {
            if ($this->proxy != false && $this->redirdl == true) {
                if (strpos($this->proxy, "|")) {
                    list($prox, $userpass) = explode("|", $this->proxy);
                    list($ip, $port) = explode(":", $prox);
                    list($user, $pass) = explode(":", $userpass);
                } else {
                    list($ip, $port) = explode(":", $this->proxy);
                }
                
                $entry = "[center][b][URL={$lik}]{$this->title} | [img]https://www.google.com/s2/favicons?domain={$domain}[/img] [color={$this->colorfn}]{$filename}[/color][color={$this->colorfs}] ({$msize})[/color]  [/b][/url][b] [br] ([color=green]You must add this proxy[/color] " . (strpos($this->proxy, "|") ? 'IP: ' . $ip . ' Port: ' . $port . ' User: ' . $user . ' & Pass: ' . $pass . '' : 'IP: ' . $ip . ' Port: ' . $port . '') . ")[/b][/center]";
                echo "<input name='176' type='text' size='100' value='{$entry}' onClick='this.select()'>";
                echo "<br>";
            } else {
                $entry = "[center][b][URL={$lik}]{$this->title} | [img]https://www.google.com/s2/favicons?domain={$domain}[/img] [color={$this->colorfn}]{$filename}[/color][color={$this->colorfs}] ({$msize}) [/color][/url][/b][/center]";
                echo "<input name='176' type='text' size='100' value='{$entry}' onClick='this.select()'>";
                echo "<br>";
            }
        }
        
        $dlhtml = "<b><a title='click here to download' href='$lik' style='TEXT-DECORATION: none' target='$tiam'> <font color='#00CC00'>" . $filename . "</font> <font color='#FF66FF'>($msize)</font> " . ($this->directdl && !$this->acc[$site]['direct'] ? "<a href='{$link}'>Direct<a> " : "") . "</a>" . ($this->proxy != false ? "<font id='proxy'>({$this->proxy})</font>" : "") . "</b>" . (($this->proxy != false && $this->redirdl == true) ? "<br/><b><font color=\"green\">You must add proxy or you can not download this link</font></b>" : "");
        return $dlhtml;
    }

    public function datecmp($a, $b)
    {
        return ($a[1] < $b[1]) ? 1 : 0;
    }
    
    public function fulllist()
    {
        $act = "";
        if ($this->act['delete'] == true) {
            $act .= '<option value="del">' . $this->lang['del'] . '</option>';
        }
        
        if ($this->act['rename'] == true) {
            $act .= '<option value="ren">' . $this->lang['rname'] . '</option>';
        }
        
        if ($act != "") {
            if ((isset($_POST['checkbox'][0]) && $_POST['checkbox'][0] != null) || isset($_POST['renn']) || isset($_POST['remove'])) {
                echo '<table style="width: 500px; border-collapse: collapse" border="1" align="center"><tr><td><center>';
                switch ($_POST['option']) {
                    case 'del':
                        $this->deljob();
                        break;
                    
                    case 'ren':
                        $this->renamejob();
                        break;
                }
                
                if (isset($_POST['renn'])) {
                    $this->renamejob();
                }
                
                if (isset($_POST['remove'])) {
                    $this->deljob();
                }
                
                echo "</center></td></tr></table><br/>";
            }
        } else {
            echo '</select>';
        }
        
        $files = array();
        foreach ($this->jobs as $job) {
            if ($job['owner'] != $this->owner && $this->privatef == true) {
                continue;
            }
            
            $files[] = array(
                urldecode($job['url']),
                $job['host'],
                $job['mtime'],
                $job['hash'],
                urldecode($job['filename']),
                $job['size'],
                $job['ip'],
                $job['msize'],
                urldecode($job['directlink']['url']),
                $job['proxy']
            );
        }
        
        if (count($files) == 0) {
            echo "<Center>" . $this->lang['notfile'] . "<br/><a href='$this->self'> [" . $this->lang['main'] . "] </a></center>";
            return;
        }
        
        echo "<script type=\"text/javascript\">function setCheckboxes(act){elts = document.getElementsByName(\"checkbox[]\");var elts_cnt  = (typeof(elts.length) != 'undefined') ? elts.length : 0;if (elts_cnt){ for (var i = 0; i < elts_cnt; i++){elts[i].checked = (act == 1 || act == 0) ? act : (elts[i].checked ? 0 : 1);} }}</script>";
        echo "<center><a href=javascript:setCheckboxes(1)> {$this->lang['checkall']} </a> | <a href=javascript:setCheckboxes(0)> {$this->lang['uncheckall']} </a> | <a href=javascript:setCheckboxes(2)> {$this->lang['invert']} </a></center><br/>";
        echo "<center><form action='$this->self' method='post' name='flist'><select onchange='javascript:void(document.flist.submit());'name='option'>";
        if ($act == "") {
            echo "<option value=\"dis\"> " . $this->lang['acdis'] . " </option>";
        } else {
            echo '<option selected="selected">' . $this->lang['ac'] . '</option>' . $act;
        }
        
        echo '</select>';
        echo '<div style="overflow: auto; height: auto; max-height: 500px; width: 713px;"><table id="table_filelist" class="filelist" align="left" cellpadding="3" cellspacing="1" width="100%"><thead><tr class="flisttblhdr" valign="bottom"><td id="file_list_checkbox_title" class="sorttable_checkbox">&nbsp;</td><td class="sorttable_alpha"><b>' . $this->lang['name'] . '</b></td>' . ($this->directdl ? '<td><b>' . $this->lang['direct'] . '</b></td>' : '') . '<td><b>' . $this->lang['original'] . '</b></td><td><b>' . $this->lang['size'] . '</b></td><td><b>' . $this->lang['date'] . '</b></td><td><b>IP</b></td></tr></thead><tbody>';
        usort($files, array(
            $this,
            'datecmp'
        ));
        $data = "";
        foreach ($files as $file) {
            $timeago = Tools_get::convert_time(time() - $file[2]) . " " . $this->lang['ago'];
            if (strlen($file[4]) > 80) {
                $file[4] = substr($file[4], 0, 70);
            }

            $linkdown = '?download=' . $file[3];
            
            $data .= "
      <tr class='flistmouseoff' align='center'>
        <td><input name='checkbox[]' value='$file[3]+++$file[4]' type='checkbox'></td>
        " . ($this->showlinkdown ? "<td><a href='$linkdown' style='font-weight: bold; color: rgb(0, 0, 0);'>$file[4]" . ($file[9] != 0 ? "<br/>({$file[9]})" : "") . "</a></td>" : "<td>$file[4]</td>") . "
        " . ($this->directdl ? "<td><a href='$file[8]' style='color: rgb(0, 0, 0);'>" . preg_replace('/(www\.|\.com|\.net|\.biz|\.info|\.org|\.us|\.vn|\.jp|\.fr|\.in|\.to|\.xyz)/', '', $file[1]) . "</a></td>" : "") . "
        <td><a href='$file[0]' style='color: rgb(0, 0, 0);'>" . preg_replace('/(www\.|\.com|\.net|\.biz|\.info|\.org|\.us|\.vn|\.jp|\.fr|\.in|\.to|\.xyz)/', '', $file[1]) . "</a></td>
        <td>" . $file[7] . "</td>
        <td><a target='_blank' href='https://www.google.com/search?q=$file[0]' title='" . $this->lang['clickcheck'] . "' target='$file[2]'><font color=#000000>$timeago</font></a></center></td><td><a target='_blank' href='http://whatismyipaddress.com/ip/$file[6]'><font color=#000000>" . $file[6] . "</font></a></td>
      </tr>";
        }
        
        $this->CheckMBIP();
        echo $data;
        $totalall      = Tools_get::convertmb($this->totalMB * 1024 * 1024);
        $MB1IP         = Tools_get::convertmb($this->countMBIP * 1024 * 1024);
        $thislimitMBIP = Tools_get::convertmb($this->limitMBIP * 1024 * 1024);
        $timereset     = Tools_get::convert_time($this->ttl * 60);
        if ($this->config['showdirect'] == true) {
            echo "</tbody><tbody><tr class='flisttblftr'><td>&nbsp;</td><td>" . $this->lang['total'] . ":</td><td></td><td></td><td>$totalall</td><td></td><td>&nbsp;</td></tr></tbody></table>
                </div></form><center><b>" . $this->lang['used'] . " $MB1IP/$thislimitMBIP - " . $this->lang['reset'] . " $timereset</b>.</center><br/>";
        } else {
            echo "</tbody><tbody><tr class='flisttblftr'><td>&nbsp;</td><td>" . $this->lang['total'] . ":</td><td></td><td>$totalall</td><td></td><td>&nbsp;</td></tr></tbody></table>
                </div></form><center><b>" . $this->lang['used'] . " $MB1IP/$thislimitMBIP - " . $this->lang['reset'] . " $timereset</b>.</center><br/>";
        }
        
    }
    
    public function deljob()
    {
        if ($this->act['delete'] == false) {
            return;
        }
        
        if (isset($_POST['checkbox'])) {
            echo "<form action='$this->self' method='post'>";
            for ($i = 0; $i < count($_POST['checkbox']); $i++) {
                $temp = explode("+++", $_POST['checkbox'][$i]);
                $ftd  = $temp[0];
                $name = $temp[1];
                echo "<br/><b> $name </b>";
                echo '<input type="hidden" name="ftd[]" value="' . $ftd . '" />';
                echo '<input type="hidden" name="name[]" value="' . $name . '" />';
            }
            
            echo "<br/><br/><input type='submit' value='" . $this->lang['del'] . "' name='remove'/> &nbsp; <input type='submit' value='" . $this->lang['canl'] . "' name='Cancel'/><br /><br />";
        }
        
        if (isset($_POST['remove'])) {
            echo "<br />";
            for ($i = 0; $i < count($_POST['ftd']); $i++) {
                $ftd  = $_POST['ftd'][$i];
                $name = $_POST['name'][$i];
                $key  = "";
                foreach ($this->jobs as $url => $job) {
                    if ($job['hash'] == $ftd) {
                        $key = $url;
                        break;
                    }
                }
                
                if ($key) {
                    unset($this->jobs[$key]);
                    echo "<center>File: <b>$name</b> " . $this->lang['deld'];
                } else {
                    echo "<center>File: <b>$name</b> " . $this->lang['notfound'];
                }
                
                echo "</center>";
            }
            
            echo "<br />";
            $this->save_jobs();
        }
        
        if (isset($_POST['Cancel'])) {
            $this->fulllist();
        }
    }
    
    public function renamejob()
    {
        if ($this->act['rename'] == false) {
            return;
        }
        
        if (isset($_POST['checkbox'])) {
            echo "<form action='$this->self' method='post'>";
            for ($i = 0; $i < count($_POST['checkbox']); $i++) {
                $temp = explode("+++", $_POST['checkbox'][$i]);
                $name = $temp[1];
                echo "<br/><b> $name </b>";
                echo '<input type="hidden" name="hash[]" value="' . $temp[0] . '" />';
                echo '<input type="hidden" name="name[]" value="' . $name . '" />';
                echo '<br/>' . $this->lang['nname'] . ': <input type="text" name="nname[]" value="' . $name . '"/ size="70"><br />';
            }
            
            echo "<br/><input type='submit' value='" . $this->lang['rname'] . "' name='renn'/> &nbsp; <input type='submit' value='" . $this->lang['canl'] . "' name='Cancel'/><br /><br />";
        }
        
        if (isset($_POST['renn'])) {
            for ($i = 0; $i < count($_POST['name']); $i++) {
                $orname = $_POST['name'][$i];
                $hash   = $_POST['hash'][$i];
                $nname  = $_POST['nname'][$i];
                $nname  = Tools_get::convert_name($nname);
                $nname  = str_replace($this->banned, '', $nname);
                if ($nname == "") {
                    echo "<br />" . $this->lang['bname'] . "<br /><br />";
                    return;
                } else {
                    echo "<br/>";
                    $key = "";
                    foreach ($this->jobs as $url => $job) {
                        if ($job['hash'] == $hash) {
                            $key = $url;
                            $jobn = array(
                                'hash' => $job['hash'],
                                'path' => $job['path'],
                                'filename' => urlencode($nname),
                                'size' => $job['size'],
                                'msize' => $job['msize'],
                                'mtime' => $job['mtime'],
                                'speed' => 0,
                                'url' => $job['url'],
                                'host' => $job['host'],
                                'owner' => $job['owner'],
                                'ip' => $job['ip'],
                                'type' => 'direct',
                                'captcha' => $job['captcha'],
                                'directlink' => array(
                                    'url' => $job['directlink']['url'],
                                    'cookies' => $job['directlink']['cookies']
                                )
                            );
                        }
                    }
                    
                    if ($key) {
                        $this->jobs[$key] = $jobn;
                        $this->save_jobs();
                        echo "File <b>$orname</b> " . $this->lang['rnameto'] . " <b>$nname</b>";
                    } else {
                        echo "File <b>$orname</b> " . $this->lang['notfound'];
                    }
                    
                    echo "<br/><br />";
                }
            }
        }
        
        if (isset($_POST['Cancel'])) {
            $this->fulllist();
        }
    }
    public function error1($msg, $a = "", $b = "", $c = "", $d = "")
    {
        if (isset($this->lang[$msg])) {
            $msg = sprintf($this->lang[$msg], $a, $b, $c, $d);
        }
        
        $msg = sprintf($this->lang["error1"], $msg);
        die($msg);
    }
    public function error2($msg, $a = "", $b = "", $c = "", $d = "")
    {
        if (isset($this->lang[$msg])) {
            $msg = sprintf($this->lang[$msg], $b, $c, $d);
        }
        
        $msg = sprintf($this->lang["error2"], $msg, $a);
        die($msg);
    }
    
    public function api_zip($url, $type)
    {
        $data = $this->curl($type . $url, '', '', 0);
        return $data;
    }
    
    public function tinyurl($url)
    {
        $data = $this->curl("http://tinyurl.com/create.php", "", "url=$url", 0);
        preg_match('/<div class="indent"><b>(.*?)<\/b><div id="success">/', $data, $match);
        return trim($match[1]);
    }
    
    public function wrong_proxy($proxy)
    {
        if (strpos($proxy, "|")) {
            list($prox, $userpass) = explode("|", $proxy);
            list($ip, $port) = explode(":", $prox);
            list($user, $pass) = explode(":", $userpass);
        } else {
            list($ip, $port) = explode(":", $proxy);
        }
        
        die('<title>You must add this proxy to IDM ' . (strpos($proxy, "|") ? 'IP: ' . $ip . ' Port: ' . $port . ' User: ' . $user . ' & Pass: ' . $pass . '' : 'IP: ' . $ip . ' Port: ' . $port . '') . '</title><center><b><span style="color:#076c4e">You must add this proxy to IDM </span> <span style="color:#30067d">(' . (strpos($proxy, "|") ? 'IP: ' . $ip . ' Port: ' . $port . ' User: ' . $user . ' and Pass: ' . $pass . '' : 'IP: ' . $ip . ' Port: ' . $port . '') . ')</span> <br><span style="color:red">PLEASE REMEMBER: IF YOU DO NOT ADD THE PROXY, YOU CAN NOT DOWNLOAD THIS LINK!</span><br><br>  Open IDM > Downloads > Options.<br><img src="http://i.imgur.com/v7FR3HE.png"><br><br>  Proxy/Socks > Choose "Use Proxy" > Add proxy server: <font color=\'red\'>' . $ip . '</font>, port: <font color=\'red\'>' . $port . '</font> ' . (strpos($proxy, "|") ? ', username: <font color=\'red\'>' . $user . '</font> and password: <font color=\'red\'>' . $pass . '</font>' : '') . ' > Choose http > OK.<br>' . (strpos($proxy, "|") ? '<img src="http://i.imgur.com/LUTpGyN.png">' : '<img src="http://i.imgur.com/zExhNVR.png">') . '<br><br>  Copy your link > Paste in IDM > OK.<br><img src="http://i.imgur.com/S355c5J.png"><br><br>  It will work > Start Download > Enjoy!<br><img src="http://i.imgur.com/vlh2vZf.png"></b></center>');
    }
}

// #################################### End class stream_get ###################################
// #################################### Begin class Tools_get ###################################

class Tools_get
{
    public function useronline()
    {
        $data   = @file_get_contents($this->fileinfo_dir . "/online.dat");
        $online = @json_decode($data, true);
        if (!is_array($online)) {
            $online = array();
            $data   = 'vng';
        }
        
        $online[$_SERVER['REMOTE_ADDR']] = time();
        
        // ## clean jobs ###
        
        $oldest = time() - 45;
        foreach ($online as $ip => $time) {
            if ($time < $oldest) {
                unset($online[$ip]);
            }
            
        }
        
        // ## clean jobs ###
        
        /*-------------- save --------------*/
        $tmp = json_encode($online);
        if ($tmp !== $data) {
            $data = $tmp;
            $fh = fopen($this->fileinfo_dir . "/online.dat", 'w') or die('<CENTER><font color=red size=3>Could not open file ! Try to chmod the folder "<B>' . $this->fileinfo_dir . '</B>" to 777</font></CENTER>');
            fwrite($fh, $data) or die('<CENTER><font color=red size=3>Could not write file ! Try to chmod the folder "<B>' . $this->fileinfo_dir . '</B>" to 777</font></CENTER>');
            fclose($fh);
            @chmod($this->fileinfo_dir . "/online.dat", 0666);
        }
        
        /*-------------- /save --------------*/
        return count($online);
    }
    
    public function size_name($link, $cookie)
    {
        if (!$link || !stristr($link, 'http')) {
            return;
        }
        
        $link   = str_replace(" ", "%20", $link);
        $port   = 80;
        $schema = parse_url(trim($link));
        $host   = $schema['host'];
        $scheme = "http://";
        if (empty($schema['path'])) {
            return;
        }
        
        $gach = explode("/", $link);
        list($path1, $path) = explode($gach[2], $link);
        if (isset($schema['port'])) {
            $port = $schema['port'];
        } elseif ($schema['scheme'] == 'https') {
            $scheme = "ssl://";
            $port   = 443;
        }
        
        if ($scheme != "ssl://") {
            $scheme = "";
        }
        $errno  = 0;
        $errstr = "";
        $hosts  = $scheme . $host . ':' . $port;
        if ($this->proxy != 0) {
            if (strpos($this->proxy, "|")) {
                list($ip, $user) = explode("|", $this->proxy);
                $auth = base64_encode($user);
            } else {
                $ip = $this->proxy;
            }
            
            $data = "GET {$path} HTTP/1.1\r\n";
            if (isset($auth)) {
                $data .= "Proxy-Authorization: Basic $auth\r\n";
            }
            
            $fp = @stream_socket_client("tcp://{$ip}", $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        } else {
            $data = "GET {$path} HTTP/1.1\r\n";
            $fp   = @stream_socket_client($hosts, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
        }
        
        $data .= "User-Agent: " . $this->UserAgent . "\r\n";
        $data .= "Host: {$host}\r\n";
        $data .= "Referer: {$this->url}\r\n";
        $data .= $cookie ? "Cookie: $cookie\r\n" : '';
        $data .= "Connection: Close\r\n\r\n";
        
        if (!$fp) {
            return -1;
        }
        
        fputs($fp, $data);
        fflush($fp);
        
        $header = "";
        do {
            if (!$header) {
                $header .= fgets($fp, 8192);
                if (!stristr($header, "HTTP/1")) {
                    break;
                }
                
            } else {
                $header .= fgets($fp, 8192);
            }
            
        } while (strpos($header, "\r\n\r\n") === false);
        
        if (stristr($header, "TTP/1.0 200 OK") || stristr($header, "TTP/1.1 200 OK") || stristr($header, "TTP/1.1 206")) {
            $filesize = trim($this->cut_str($header, "Content-Length:", "\n"));
        } else {
            $filesize = -1;
        }
        
        if (!is_numeric($filesize)) {
            $filesize = -1;
        }
        
        $filename = "";
        
        if (stristr($header, "filename")) {
            if (preg_match("/; filename=(.*?);/", $header, $match)) {
                $filename = trim($match[1]);
            } else {
                $filename = trim($this->cut_str($header, "filename", "\n"));
            }
            
        } else {
            $filename = substr(strrchr($link, '/'), 1);
        }
        
        $filename = self::convert_name($filename);
        return array(
            $filesize,
            $filename
        );
    }
    
    public function site_hash($url)
    {
        $schema = parse_url($url);
        $site   = preg_replace("/(www\.|\.com|\.net|\.biz|\.info|\.org|\.us|\.vn|\.jp|\.fr|\.in|\.to|\.xyz)/", "", $schema['host']);
        return $site;
    }
    
    public function convert($filesize)
    {
        $filesize = str_replace(",", ".", $filesize);
        if (preg_match('/^([0-9]{1,4}+(\.[0-9]{1,2})?)/', $filesize, $value)) {
            if (stristr($filesize, "TB")) {
                $value = $value[1] * 1024 * 1024 * 1024 * 1024;
            } elseif (stristr($filesize, "GB")) {
                $value = $value[1] * 1024 * 1024 * 1024;
            } elseif (stristr($filesize, "MB")) {
                $value = $value[1] * 1024 * 1024;
            } elseif (stristr($filesize, "KB")) {
                $value = $value[1] * 1024;
            } else {
                $value = $value[1];
            }
            
        } else {
            $value = 0;
        }
        
        return $value;
    }
    
    public function convertmb($filesize)
    {
        if (!is_numeric($filesize)) {
            return $filesize;
        }
        
        $soam = false;
        if ($filesize < 0) {
            $filesize = abs($filesize);
            $soam     = true;
        }
        
        if ($filesize >= 1024 * 1024 * 1024 * 1024) {
            $value = ($soam ? "-" : "") . round($filesize / (1024 * 1024 * 1024 * 1024), 2) . " TB";
        } elseif ($filesize >= 1024 * 1024 * 1024) {
            $value = ($soam ? "-" : "") . round($filesize / (1024 * 1024 * 1024), 2) . " GB";
        } elseif ($filesize >= 1024 * 1024) {
            $value = ($soam ? "-" : "") . round($filesize / (1024 * 1024), 2) . " MB";
        } elseif ($filesize >= 1024) {
            $value = ($soam ? "-" : "") . round($filesize / (1024), 2) . " KB";
        } else {
            $value = ($soam ? "-" : "") . $filesize . " Bytes";
        }
        
        return $value;
    }
    
    public function uft8html2utf8($s)
    {
        if (!function_exists('uft8html2utf8_callback')) {
            function uft8html2utf8_callback($t)
            {
                $dec = $t[1];
                if ($dec < 128) {
                    $utf = chr($dec);
                } else if ($dec < 2048) {
                    $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                    $utf .= chr(128 + ($dec % 64));
                } else {
                    $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                    $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                    $utf .= chr(128 + ($dec % 64));
                }
                
                return $utf;
            }
        }
        
        return preg_replace_callback('|&#([0-9]{1,});|', 'uft8html2utf8_callback', $s);
    }
    
    public function convert_name($filename)
    {
        $filename = urldecode($filename);
        $filename = Tools_get::uft8html2utf8($filename);
        $filename = preg_replace("/(\]|\[|\@|\"\;\?\=|\"|=|\*|UTF-8|\')/U", "", $filename);
        $filename = preg_replace("/(HTTP|http|WWW|www|\.html|\.htm)/i", "", $filename);
        $filename = str_replace($this->banned, '.xxx', $filename);
        if (empty($filename) == true) {
            $filename = substr(md5(time() . $url), 0, 10);
        }
        
        return $filename;
    }
    
    public function convert_time($time)
    {
        if ($time >= 86400) {
            $time = round($time / (60 * 24 * 60), 1) . " " . $this->lang['days'];
        } elseif (86400 > $time && $time >= 3600) {
            $time = round($time / (60 * 60), 1) . " " . $this->lang['hours'];
        } elseif (3600 > $time && $time >= 60) {
            $time = round($time / 60, 1) . " " . $this->lang['mins'];
        } else {
            $time = $time . " " . $this->lang['sec'];
        }
        
        return $time;
    }
}
// #################################### End class Tools_get #####################################
// #################################### Begin class Download ####################################
class Download
{
    public $last = false;
    public function __construct($lib, $site)
    {
        $this->lib  = $lib;
        $this->site = $site;
    }
    
    public function error($msg, $force = false, $delcookie = true, $type = 1)
    {
        if (isset($this->lib->lang[$msg])) {
            $msg = sprintf($this->lib->lang[$msg], $this->site, $this->url);
        }
        
        $msg = sprintf($this->lib->lang["error{$type}"], $msg, $this->url);
        if ($delcookie) {
            $this->save();
        }
        
        if ($force || $this->last) {
            die($msg);
        }
        
    }
    
    public function filter_cookie($cookie, $del = array('', '""', 'deleted'))
    {
        $cookie  = explode(";", $cookie);
        $cookies = "";
        $a       = array();
        foreach ($cookie as $c) {
            $delete = false;
            $pos    = strpos($c, "=");
            $key    = str_replace(" ", "", substr($c, 0, $pos));
            $val    = substr($c, $pos + 1);
            foreach ($del as $dul) {
                if ($val == $dul) {
                    $delete = true;
                }
                
            }
            if (!$delete) {
                $a[$key] = $val;
            }
            
        }
        foreach ($a as $b => $c) {
            $cookies .= "{$b}={$c}; ";
        }
        return $cookies;
    }
    
    public function save($cookies = "", $save = true)
    {
        $cookie = $cookies != "" ? $this->filter_cookie(($this->lib->cookie ? $this->lib->cookie . ";" : "") . $cookies) : "";
        if ($save) {
            $this->lib->save_cookies($this->site, $cookie);
        }
        
        $this->lib->cookie = $cookie;
    }
    
    public function exploder($del, $data, $i)
    {
        $a = explode($del, $data);
        return $a[$i];
    }

    public function isredirect($data)
    {
        if (preg_match('/ocation: (.*)/', $data, $match)) {
            $this->redirect = trim($match[1]);
            return true;
        } else {
            return false;
        }
        
    }
    
    public function getredirect($link, $cookie = "")
    {
        $data = $this->lib->curl($link, $cookie, "", -1);
        if (preg_match('/ocation: (.*)/', $data, $match)) {
            $link = trim($match[1]);
        }
        
        $cookies = $this->lib->GetCookies($data);
        if ($cookies != "") {
            $this->save($cookies);
        }
        
        return $link;
    }
    
    public function passredirect($data, $cookie)
    {
        if (stristr($data, "302 Found") && stristr($data, "ocation")) {
            preg_match('/ocation: (.*)/', $data, $match);
            $data = $this->lib->curl(trim($match[1]), $cookie, "");
        }
        return $data;
    }
    
    public function parseForm($data)
    {
        $post = array();
        if (preg_match_all('/<input(.*)>/U', $data, $matches)) {
            foreach ($matches[0] as $input) {
                if (!stristr($input, "name=")) {
                    continue;
                }
                
                if (preg_match('/name=(".*"|\'.*\')/U', $input, $name)) {
                    $key = substr($name[1], 1, -1);
                    if (preg_match('/value=(".*"|\'.*\')/U', $input, $value)) {
                        $post[$key] = substr($value[1], 1, -1);
                    } else {
                        $post[$key] = "";
                    }
                    
                }
            }
        }
        return $post;
    }
    
    public function linkpassword($url)
    {
        $password = "";
        if (strpos($url, "|")) {
            $linkpass = explode('|', $url);
            $url      = $linkpass[0];
            $password = $linkpass[1];
        }
        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }
        
        return array(
            $url,
            $password
        );
    }
    
    public function forcelink($link, $a)
    {
        $link = str_replace(" ", "%20", $link);
        for ($i = 0; $i < $a; $i++) {
            if ($size = $this->lib->getsize($link, $this->lib->cookie) <= 0) {
                $link = $this->getredirect($link, $this->lib->cookie);
            } else {
                return $link;
            }
            
        }
        $this->error("cantconnect", false, false);
        return false;
    }
    
    public function General($url)
    {
        $this->url      = $url;
        $this->lib->url = $url;
        $this->cookie   = '';

        if (method_exists($this, "PreLeech")) {
            $this->PreLeech($this->url);
        }
        if (method_exists($this, "FreeLeech")) {
            $link = $this->FreeLeech($this->url);
            if ($link) {
                $link = $this->forcelink($link, 2);
                if ($link)
                    return $link; 
            }
        }

        $maxacc = count($this->lib->acc[$this->site]['accounts']);
        if ($maxacc == 0) 
            $this->error('noaccount', true);
        for ($k = 0; $k < $maxacc; $k++) {
            $account = trim($this->lib->acc[$this->site]['accounts'][$k]);
            if (stristr($account, ':'))
                list($user, $pass) = explode(':', $account);
            else
                $cookie = $account;
            
            if (!empty($cookie) || (!empty($user) && !empty($pass))) {
                for ($j = 0; $j < 2; $j++) {
                    if (($maxacc - $k) == 1 && $j == 1)
                        $this->last = true;
                    /* Export cookie & Check */
                    if (empty($cookie))
                        $cookie = $this->lib->get_cookie($this->site);  
                    if (empty($cookie)) {
                        $cookie = false;
                        if (method_exists($this, "Login"))
                            $cookie = $this->Login($user, $pass);
                    }
                    if (!$cookie)
                        continue;
                    
                    $this->save($cookie);
                    if (method_exists($this, "CheckAcc")) 
                        $status = $this->CheckAcc($this->lib->cookie);
                    else 
                        $status = array(true, "Without Acc Checker");
                    
                    if ($status[0]) {
                        $link = false;
                        if (method_exists($this, "Leech"))
                            $link = $this->Leech($this->url);
                        if ($link) {
                            $link = $this->forcelink($link, 3);
                            if ($link) 
                                return $link;
                        }
                        else
                            $this->error('pluginerror');
                    } 
                    else
                        $this->error($status[1]);
                }
            }
        }
        return false;
    }
}
// #################################### End class Download ####################################

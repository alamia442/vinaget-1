<?php
$host = array(); $debrid = array(); $alias = array();
$alias['dfpan.com'] = 'yunfile.com';
$alias['yfdisk.com'] = 'yunfile.com';
$alias['filemarkets.com'] = 'yunfile.com';
$alias['tadown.com'] = 'yunfile.com';
$alias['up.4share.vn'] = '4share.vn';
$alias['ul.to'] = 'uploaded.net';
$alias['uploaded.to'] = 'uploaded.net';
/* Host */
$folderhost = opendir("hosts/");
while ($hostname = readdir($folderhost)) {		
	if ($hostname == "." || $hostname == ".." || strpos($hostname, "bak") || $hostname == "hosts.php") 
		continue;
	if (stripos($hostname, "php")) {
		$site = str_replace("_", ".", substr($hostname, 0, -4));
		if(isset($alias[$site])) {
			$host[$site] = array(
				'alias' => true,
				'site' => $alias[$site],
				'file' => str_replace(".", "_", $alias[$site]).".php",
				'class' => "dl_" . str_replace(array(".", "-"), "_", $alias[$site])
			);
		}
		else {
			$host[$site] = array(
				'alias' => false,
				'site' => $site,
				'file' => $hostname,
				'class' => "dl_" . str_replace(array(".", "-"), "_", $site)
			);
		}
	}
}
closedir($folderhost);
/* Debrid */
$folderhost = opendir("hosts/debrid/");
while ($hostname = readdir($folderhost)) {		
	if ($hostname == "." || $hostname == ".." || strpos($hostname, "bak")) 
		continue;
	if (stripos($hostname, "php")) {
		$site = str_replace("_", ".", substr($hostname, 0, -4)); 
		$debrid[$site] = array(
			'alias' => false,
			'site' => $site,
			'file' => $hostname,
			'class' => "dl_" . str_replace(array(".", "-"), "_", $site)
		);
	}
}
closedir($folderhost);
?>

<?php
/*
* New Vinaget by LTT❤
* Version: 3.3 LTSB
* Based on Vinaget 2.7.0 Final Revision 93
* Description: 
	- Vinaget is script generator premium link that allows you to download files instantly and at the best of your Internet speed.
	- Vinaget is your personal proxy host protecting your real IP to download files hosted on hosters like RapidShare, megaupload, hotfile...
	- You can now download files with full resume support from filehosts using download managers like IDM etc
	- Vinaget is a Free Open Source, supported by a growing community.
* Code LeechViet by VinhNhaTrang
* Developed by - ..:: [H] ::..
			   - [FZ]
			   - LTT❤
*/
$using = isset($_COOKIE['using']) ? $_COOKIE['using'] : 'default';
$using = isset($_REQUEST['using']) ? $_REQUEST['using'] : $using;
setcookie('using', $using);
ob_start();
ob_implicit_flush(TRUE);
ignore_user_abort(0);
if (!ini_get('safe_mode')) set_time_limit(30);
define('vinaget', 'yes');
require_once('class.php');
$obj = new stream_get(); 
$obj->using = $using;
$obj->msg = false;
if (!empty($_COOKIE['msg'])) $obj->msg = htmlspecialchars($_COOKIE['msg']);
setcookie('msg', '');
$host = $obj->list_host;
$debrid = $obj->list_host_debrid;
$skin = "skin/{$obj->skin}";
error_reporting($obj->display_error ? E_ALL : 0);
if ($obj->Deny == false) {
	require_once("{$skin}/function.php");
	if (isset($_POST['urllist'])) 
		$obj->main();
	else if (isset($_GET['infosv'])) 
		showStat();
	else if (!isset($_POST['urllist'])) 
		include("{$skin}/index.php");
}
else {
	if (!$obj->hide_page)
		include("{$skin}/login.php");
	else {
		if (isset($_GET['secure']))
			include("{$skin}/login.php");
		else
			include("{$skin}/hide.php");
	} 
}
ob_end_flush();
?>
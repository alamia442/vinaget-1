<?php
echo '<h3><a href="?id=admin&page=config">Config</a> | 
	  <a href="?id=admin&page=host">Host</a> | 
	  <a href="?id=admin&page=account">Account</a> |
	  <a href="?id=admin&page=debrid">Debrid</a> | 
	  <a href="?id=admin&page=cookie">Cookie</a> | 
	  <a href="?id=admin&page=debug">Debug</a></h3>';

$page = isset($_GET['page']) ? $_GET['page'] : 'config';
/* Debug */
if ($page == "debug") 
	echo "<form method='POST' action='debug.php' target='debug'>";
else 
	echo "<form method='POST' action='proccess.php?page={$page}'>";
if( $obj->msg) 
	echo "<b>{$obj->msg}</b>";
/* Config */
if ($page == 'config') {
	include("config.php");
	echo '<table id="tableCONFIG" class="filelist" align="left" cellpadding="3" cellspacing="1" width="100%">
			<tr class="flisttblhdr" valign="bottom">
				<td align="center" colspan="2"><B>CONFIG</B></td>
			</tr>
		';
	if ($handle = opendir('lang/')) {
		$blacklist = array('.', '..', '', ' ');
		$lang = "<select name='config[language]'>";
		while (false !== ($file = readdir($handle))) {
			if (!in_array($file, $blacklist))
				$lang .= "<option value='".substr($file,0,-4)."' ".(substr($file,0,-4)==$obj->config['language'] ? "selected" : "").">".substr($file,0,-4)."</option>";
		}
		$lang .= "</select>";
		closedir($handle);
	}
	if ($handle = opendir('skin/')) {
		$blacklist = array('.', '..', '', ' ');
		$skin = "<select name='config[skin]'>";
		while (false !== ($file = readdir($handle))) {
			if (!in_array($file, $blacklist))
				$skin .= "<option value='".$file."' ".($file==$obj->config['skin'] ? "selected" : "").">".$file."</option>";
		}
		$skin .= "</select>";
		closedir($handle);
	}
	unset($obj->config['skin']);
	unset($obj->config['language']);
	foreach($obj->config as $ckey => $cval) {
		if ($ckey == "debrid_mode" || $ckey == "debrid_host")
			continue;
		echo '<tr class="flistmouseoff"><td><i><b>'.$ckey.'</b></i></td><td style="text-align:right">';
		if(gettype($cval) == 'string' || gettype($cval) == 'integer') {
			if ($ckey == "api_ads") echo '<input size="50" type="text" name="config['.$ckey.']" value="'.$cval.'" placeholder="only support sh.st, ouo.io, adf.ly, bc.vc" spellcheck="false" autocomplete="off">';
			else echo '<input size="50" type="text" name="config['.$ckey.']" value="'.$cval.'" spellcheck="false" autocomplete="off">';
		}
		elseif(gettype($cval) == 'boolean') echo '<label for="config['.$ckey.'][\'on\']"><input type="radio" id="config['.$ckey.'][\'on\']" value="on" name="config['.$ckey.']"'.($cval ? ' checked="checked"' : '').'/> On</label> <label for="config['.$ckey.'][\'off\']"><input type="radio" id="config['.$ckey.'][\'off\']" value="off" name="config['.$ckey.']"'.(!$cval ? ' checked="checked"' : '').'/> Off</label>';
		echo '</td></tr>';
	}
	
	echo '<tr class="flistmouseoff"><td><i><b>language</b></i></td><td style="text-align:right">'.$lang.'</td></tr>';
	echo '<tr class="flistmouseoff"><td><i><b>skin</b></i></td><td style="text-align:right">'.$skin.'</td></tr>';
	echo "</table>";
?>	<br/>&nbsp;
	<center>
		<input id='submit' type='submit' name="submit" value='Save Config'/>
	</center>
	<br/>
<?php
}
/* Cookie */
else if ($page == 'cookie') {
?>
	<table>
	<tr>
	<td>
	<?php printf($obj->lang['acctype']); ?> 
		<select name='type' id='type'>
	<?php
		if ($obj->debrid_mode) {
			foreach ($debrid as $key => $val) {
				if ($key != $obj->debrid_host)
					continue;
				if (!$val['alias']) {
					require_once('hosts/debrid/' . $val['file']);
					if (method_exists($val['class'], "CheckAcc")) echo "<option value='{$key}'>{$key}</option>";
				}				
			}
		}
		else {
			foreach ($host as $key => $val) {
				if (!$val['alias']) {
					require_once('hosts/' . $val['file']);
					if (method_exists($val['class'], "CheckAcc")) echo "<option value='{$key}'>{$key}</option>";
				}
			}
		}
	?>
	</select>
	</td>
	<td>
		&nbsp; &nbsp; &nbsp; <input type="text" name="cookie" id="accounts" value="" size="50"><br />
	</td>
	<td>
		&nbsp; &nbsp; &nbsp; <input type=submit value="Submit">
	</td>
	</tr>
	</table>
<?php
	echo '<table id="tableCOOKIE" class="filelist" align="left" cellpadding="3" cellspacing="1" width="713px">
			<tr class="flisttblhdr" valign="bottom">
				<td align="center" colspan="3"><B>COOKIE</B></td>
			</tr>
		';
	foreach ($obj->cookies as $ckey => $cookies) {
		if ($obj->debrid_mode) {
			if ($ckey != $obj->debrid_host) 
				continue;
		}
		else {
			if ($ckey == $obj->debrid_host) 
				continue;			
		}
		if ($cookies['cookie'] != "") 
			echo '<tr class="flistmouseoff"><td><B>'.$ckey.'</B></td><td style="word-break:break-all">'.$cookies['cookie'].'</td><td width="1"><B><a style="color: black;" href="proccess.php?page=cookie&del='.$ckey.'">[DELETE]</a></B></td></tr>';
	}
	echo "</table>";
}

/* Account */
else if ($page == 'account') {
?>
	<table>
	<tr>
	<td>
	<?php printf($obj->lang['acctype']); ?> 
		<select name='type' id='type'>
	<?php
	var_dump($debrid);
		if ($obj->debrid_mode) {
			foreach ($debrid as $key => $val) {
				if ($key != $obj->debrid_host)
					continue;
				if (!$val['alias']) {
					require_once('hosts/debrid/' . $val['file']);
					if (method_exists($val['class'], "CheckAcc")) 
						echo "<option value='{$key}'>{$key}</option>";
				}				
			}
		}
		else {
			foreach($host as $key => $val) {
				if (!$val['alias']) {
					require_once('hosts/' . $val['file']);
					if (method_exists($val['class'], "CheckAcc")) echo "<option value='{$key}'>{$key}</option>";
				}
			}
		}
	?>
	</select>
	</td>
	<td>
		&nbsp; &nbsp; &nbsp; <textarea type="text" name="account" id="accounts" value="" rows="5" cols="50" placeholder="one account per line"></textarea><br />
	</td>
	<td>
		&nbsp; &nbsp; &nbsp; <input type=submit value="Submit">
	</td>
	</tr>
	</table>
<?php
	echo '<table id="tableAccount" class="filelist" align="left" cellpadding="3" cellspacing="1" width="713px">
			<tr class="flisttblhdr" valign="bottom">
				<td align="center" colspan="3"><B>Account</B></td>
			</tr>
		';
	foreach ($obj->acc as $ckey => $val) {
		$max = count($val['accounts']);
		if ($obj->debrid_mode) {
			if ($ckey != $obj->debrid_host) 
				continue;
		}
		else {
			if ($ckey == $obj->debrid_host) 
				continue;			
		}
		if ($max != 0) {
			for ($i = 0; $i < $max; $i++) {
				echo '<tr class="flistmouseoff"><td><B>'.$ckey.'</B></td><td style="word-break:break-all">'.$val['accounts'][$i].'</td><td width="1"><B><a style="color: black;" href="proccess.php?page=account&del='.$i.'&host='.$ckey.'">[DELETE]</a></B></td></tr>';
			}
		}
	}
	echo "</table>";
}
/* Debrid */
else if ($page == 'debrid') {
	include("config.php");
	echo '<table id="tableDEBRID" class="filelist" align="left" cellpadding="3" cellspacing="1" width="100%">
			<tr class="flisttblhdr" valign="bottom">
				<td align="center" colspan="2"><B>DEBRID</B></td>
			</tr>
		';	
	foreach ($obj->config as $ckey => $cval) {
		if ($ckey == "debrid_mode")
			echo '<tr class="flistmouseoff"><td><i><b>'.$ckey.'</b></i></td><td style="text-align:right"><label for="config['.$ckey.'][\'on\']"><input type="radio" id="config['.$ckey.'][\'on\']" value="on" name="config['.$ckey.']"'.($cval ? ' checked="checked"' : '').'/> On</label> <label for="config['.$ckey.'][\'off\']"><input type="radio" id="config['.$ckey.'][\'off\']" value="off" name="config['.$ckey.']"'.(!$cval ? ' checked="checked"' : '').'/> Off</label></td></tr>';
		else if ($ckey == "debrid_host") {
			echo '<tr class="flistmouseoff"><td><i><b>'.$ckey.'</b></i></td><td style="text-align:right">';
			echo "<select name='config[{$ckey}]' id='config[{$ckey}]'>";
			foreach ($debrid as $key => $val)
				echo "<option value='{$key}' " . ($key == $obj->debrid_host ? "selected" : "") . ">{$key}</option>";
			echo '</select></td></tr>';
		}
	}
	echo '</table>';
	?>
	<br/>&nbsp;
	<center>
		<input id='submit' type='submit' name="submit" value='Save Config'/>
	</center>
	<br/>
	<?php
}
/* Host */
else if ($page == 'host') {
	echo '<table id="tableHOST" class="filelist" align="left" cellpadding="3" cellspacing="1" width="100%">
			<tr class="flisttblhdr" valign="bottom">
				<td align="center"><B>Host</B></td>
				<td align="center"><B>Max Size</B></td>
				<td align="center"><B>Proxy</B></td>
				<td align="center"><B>Direct</B></td>
			</tr>
		';
	foreach ($obj->acc as $ckey => $val) {
		if ($obj->debrid_mode) {
			if ($ckey != $obj->debrid_host)
				continue;
		}
		else
			if ($ckey == $obj->debrid_host)
				continue;			
		echo '<tr class="flistmouseoff">
				<td><B>'.$ckey.'</B></td>
				<td><input type="text" name="host['.$ckey.'][max_size]" value="'.$val['max_size'].'"/></td>
				<td><input type="text" name="host['.$ckey.'][proxy]" value="'.$val['proxy'].'"/></td>
				<td><input type="checkbox" name="host['.$ckey.'][direct]" value="ON" '.($val['direct'] ? 'checked' : '').'/></td>
			</tr>';
	}
	echo "</table>";
	echo "&nbsp;<br/><input id='submit' type='submit' name='submit' value='Save Changes'/><br/>&nbsp;";
}
/* Debug */
else if ($page == 'debug') {
?>
<table style="width:70%;">
	<tr><td>URL </td><td> : </td><td><input type="text" id="link" name="link" style="width:100%;"></td></tr>
	<tr><td>POST</td><td> : </td><td><input type="text" id="post" name="post" style="width:100%;"></td></tr>
	<tr><td>COOKIE</td><td> : </td><td><input type="text" id="cookie" name="cookie" style="width:100%;"></td></tr>
	<tr><td>PROXY</td><td> : </td><td><input type="text" id="proxy" name="proxy" style="width:100%;"></td></tr>
</table>
<input type='submit' value='Debug'>
<input type='button' onClick="form.reset()" value='Reset'>
</form>
<br/>
<iframe name="debug" width="700" height="400" style="background:white" src="debug.php"></iframe>
<?php
}
else{
	echo "<b>Page not available</b>";
}
echo "</form>";
?>
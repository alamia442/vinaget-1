<?php
	class dl_openload_co extends Download {
		public function Freeleech($url) {
			if (preg_match('/https?:\/\/(openload|oload)\.(io|co|tv|stream|win|download|info)\/(embed|f)\/([a-zA-z0-9-_]+)/i', $url, $matches)) {
				$page = $this->lib->curl("https://api.openload.co/1/file/dlticket?file={$matches[4]}&login=81f50586bcbabec5&key=9ApzPFcJ", '', '', 0);
				$json = json_decode($page, true);
				if ($json['status'] == 200) {
					$page = $this->lib->curl("https://api.openload.co/1/file/dl?file={$matches[4]}&ticket={$json['result']['']}&login=81f50586bcbabec5&key=9ApzPFcJ", '', '', 0);
					$json = json_decode($page, true);					
				}
				else
					$this->error($json['msg'], true, false, 2);	
			}
		}
	}
?>
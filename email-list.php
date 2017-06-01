<html>
	<body>

	<?php
		
		$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
		$username = '******@gmail.com';
		$password = '******';
	
		if(!isset($_POST["title"]))
		{
			echo "<div><a href='first.php'>Please Input Search Key</a><div>";
		}
		else
		{
			$inbox = imap_open($imapPath, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
			$emails = imap_search($inbox,'ALL'); 
			$output = '';
			$count = 0;
			
			$nowDate = new DateTime();
			$nowDate = $nowDate->getTimestamp();
					
			if(sizeof($emails) > 0){
				$output .= '<div style="text-align:center;">';
				foreach($emails as $mail) {
					
					$headerInfo = imap_header($inbox, $mail);		
					$t = $headerInfo->subject;
					
					$date = strtotime("".$headerInfo->date."");					
					
					if($nowDate - $date < 1800)
					{
						$output .= '<div style="text-align:center;">';
						$output .= "<p><b>Subject: </b>".$headerInfo->subject.'</p>';
						$output .= "<p><b>Date: </b>".$headerInfo->date.'</p>';
						$output .= "<p><b>From: </b>".preg_replace("/[<>]/", "", $headerInfo->fromaddress).'</p>';
						$output .= "<p><b>Message ID: </b>".$mail.'</p>';
						
						$uid = imap_uid($inbox, $mail);
						$body = getBody($uid, $inbox);
						$output .= "<p><b>Message: </b>".$body.'<br/><br/></p>';
						
						$count++;
					}
				}
				if($count == 0)
				{
					$output .= "<div><a href='first.php'>No Result</a><div>";
				}
				$output .= '</div>';
				echo $output;
				$output = '';						
			}
			
			// colse the connection
			imap_expunge($inbox);
			imap_close($inbox);
		}
		
		function getpart($mbox, $mid, $p, $partno) {
		
		}
		
		function getBody($uid, $imap) {
			$body = get_part($imap, $uid, "TEXT/HTML");
			// if HTML body is empty, try getting text body
			if ($body == "") {
				$body = get_part($imap, $uid, "TEXT/PLAIN");
			}
			return $body;
		}

		function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
			if (!$structure) {
			   $structure = imap_fetchstructure($imap, $uid, FT_UID);
			}
			if ($structure) {
				if ($mimetype == get_mime_type($structure)) {
					if (!$partNumber) {
						$partNumber = 1;
					}
					$text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
					switch ($structure->encoding) {
						case 3: return imap_base64($text);
						case 4: return imap_qprint($text);
						default: return $text;
				   }
			   }

				// multipart 
				if ($structure->type == 1) {
					foreach ($structure->parts as $index => $subStruct) {
						$prefix = "";
						if ($partNumber) {
							$prefix = $partNumber . ".";
						}
						$data = get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
						if ($data) {
							return $data;
						}
					}
				}
			}
			return false;
		}

		function get_mime_type($structure) {
			$primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

			if ($structure->subtype) {
			   return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
			}
			return "TEXT/PLAIN";
		}
	
	?>
	
	<form action="email-delete.php" method="post">
		MESSAGE ID: <input type="text" name="mid"><br>
		<input type="submit">
	</form>

	</body>
</html>
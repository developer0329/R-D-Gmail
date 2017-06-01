<?php
	$imapPath = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = '*******@gmail.com';
	$password = '*******';
	
	// try to connect 
	$inbox = imap_open($imapPath, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
	
	if(isset($_POST["mid"])){
		$check = imap_mailboxmsginfo($inbox);
		echo "Messages before delete: " . $check->Nmsgs . "<br />\n";

		imap_delete($inbox, $_POST["mid"]);

		$check = imap_mailboxmsginfo($inbox);
		echo "Messages after  delete: " . $check->Nmsgs . "<br />\n";

		imap_expunge($inbox);

		$check = imap_mailboxmsginfo($inbox);
		echo "Messages after expunge: " . $check->Nmsgs . "<br />\n";

	}	
	// colse the connection
	imap_expunge($inbox);
	imap_close($inbox);
	
	
?>
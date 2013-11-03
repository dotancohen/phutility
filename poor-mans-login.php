<?php

// No database, no session!

$salt = 'Some tasty salt';
$usernameSha1 = 'fde3b9e88dca861202148dea3a7cec8b05befbc9';
$passwordSha1 = '85987fb0a90e77a0bdeeb5a2026fa8a4a67137f6';

$isAllowedIn  = FALSE;
$secureIdentifier = sha1($salt.$_SERVER['HTTP_USER_AGENT']);

if ( isset($_POST['username']) && isset($_POST['password']) ) {

	setcookie('login', 'garbage', 0);

	if ( sha1($salt.$_POST['username'])==$usernameSha1 && sha1($salt.$_POST['password'])==$passwordSha1 ) {
		$sevenDays = time() + 7*24*60*60;
		setcookie('login', $secureIdentifier, $sevenDays);
		$_COOKIE['login'] = $secureIdentifier;
		$isAllowedIn = TRUE;
	}

} else if ( $_COOKIE['login']==$secureIdentifier ) {
	$isAllowedIn = TRUE;
}


// From here check $isAllowedIn variable


?>

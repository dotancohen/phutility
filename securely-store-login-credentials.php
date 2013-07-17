<?php



/**
 * Securely store third-party login credentials without revealing them in cookies or on the server.
 *
 * Some UAs may not accept cookies, and therefore this function may return TRUE even if the data is not stored.
 * This function relies on mcrypt: sudo aptitude install openssl php5-mcrypt
 * To use this function, run session_start() at the beginning of the script.
 *
 * @author     Dotan Cohen
 * @version    2013-06-16
 *
 * @param  array  $credentials An array with the keys 'username' and 'password'
 * @param  string $service     The name of the service for which the credentials are to be stored
 *
 * @return bool
 */
function securely_store_login_credentials($credentials, $service)
{
	if ( !is_string($credentials['username']) || !is_string($credentials['password']) || !is_string($service)) {
		return NULL;
	}

	$encryptionKey = openssl_random_pseudo_bytes(64, $crypto_strong);

	if ( !$crypto_strong ) {
		return FALSE;
	}

	setcookie(md5($service), $encryptionKey, 0);
	$_COOKIE[md5($service)] = $encryptionKey;

	$_SESSION['username-'.$service] = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryptionKey, $credentials['username'], MCRYPT_MODE_CFB);
	$_SESSION['password-'.$service] = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryptionKey, $credentials['password'], MCRYPT_MODE_CFB);

	return TRUE;
}




/**
 * Securely retrive third-party login credentials
 *
 * @author     Dotan Cohen
 * @version    2013-06-16
 *
 * @param  string $service The name of the service for which the credentials are stored
 *
 * @return array
 */
function securely_retrieve_login_credentials($service)
{
	if ( !is_string($service) ) {
		return NULL;
	}

	if ( !isset($_SESSION['username-'.$service]) || !isset($_SESSION['password-'.$service]) || !isset($_COOKIE[md5($service)]) ) {
		return FALSE;
	}

	$credentials = array();
	$credentials['username'] = mcrypt_decrypt(MCRYPT_BLOWFISH, $_COOKIE[md5($service)], $_SESSION['username-'.$service], MCRYPT_MODE_CFB);
	$credentials['password'] = mcrypt_decrypt(MCRYPT_BLOWFISH, $_COOKIE[md5($service)], $_SESSION['password-'.$service], MCRYPT_MODE_CFB);

	return $credentials;
}



?>

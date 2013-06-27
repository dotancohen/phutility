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



/**
 * Send email via Amazon SES
 *
 * The AWSSDKforPHP library must be installed first!
 * http://aws.amazon.com/sdkforphp/
 *
 * @author     Dotan Cohen
 * @version    2013-06-12
 *
 * @param  string|array $to      The addresss(es) of the intended recipients of the mail
 * @param  string       $subject The subject of the mail
 * @param  string       $message The text of the mail
 * @param  string       $to      The addresss of the sender of the mail
 * @param  string|array $cc      The addresss(es) to which copies of the mail should be sent
 * @param  string|array $bcc     The addresss(es) to which copies of the mail should be surreptitiously sent
 *
 * @return bool
 */
function send_email_ses($to, $subject, $message, $from, $cc=NULL, $bcc=NULL)
{
	require_once('AWSSDKforPHP/sdk.class.php');
	require_once('AWSSDKforPHP/services/ses.class.php');

	$amazonSes = new AmazonSES();
	$addresses = array();
	$message = array();

	if (is_array($to)) {
		$addresses['ToAddresses'] = $to;
	} else {
		$addresses['ToAddresses'] = array($to);
	}

	if ( $cc!==NULL ) {
		if (is_array($cc)) {
			$addresses['CcAddresses'] = $cc;
		} else {
			$addresses['CcAddresses'] = array($cc);
		}
	}

	if ( $bcc!==NULL ) {
		if (is_array($bcc)) {
			$addresses['BccAddresses'] = $bcc;
		} else {
			$addresses['BccAddresses'] = array($bcc);
		}
	}

	$message = array(
			'Subject.Charset'   => 'UTF-8',
			'Body.Text.Charset' => 'UTF-8',
			'Subject.Data'      => $subject,
			'Body.Text.Data'    => $message
			);

	$amazonSes->send_email($from, $addresses, $message);

	return $response->isOK();
}



/**
 * Ensure that all necessary array elements are present and not empty.
 *
 * Pass to this function the array to check in the first parameter, then
 * in each additional parameter pass in strings which represent array
 * elements which must be present and not empty.
 *
 * @author     Dotan Cohen
 * @version    2013-06-03
 *
 * @param  array   $consideration  Array to be checked
 * @param  string  ...             Elements which must exist in array and not be empty strings.
 *
 * @return bool
 */
function ensure_fields($consideration)
{
	if ( !is_array($consideration) ) {
		return NULL;
	}

	$args = func_get_args();
	$pass = 0;
	foreach ( $args as $a ) {
		if ( $pass++ == 0 ) {
			continue;
		}
		if ( !is_string($a) ) {
			return NULL;
		}
		if ( !isset($consideration[$a]) || $consideration[$a]=='' ) {
			return FALSE;
		}
	}

	return TRUE;
}



/**
 * Get the IP address used for external internet-facing applications.
 *
 * @author     Dotan Cohen
 * @version    2013-06-09
 *
 * @return string
 */
function get_external_ip_address()
{
	// TODO: Add a fallback to http://httpbin.org/ip

	$url="simplesniff.com/ip";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}



/**
 * Get the IP address used for external internet-facing applications.
 *
 * @author     Dotan Cohen
 * @version    2013-06-16
 *
 * @param bool $force_string Force the return of a single address as a string, even if more than one address is found
                             True: Always return a string with a single value
                             False: Always return an array
                             Null (empty): Return a string if a single value, array for multiple values
 *
 * @return bool|string|array
 */
function get_user_ip_address($force_string=NULL)
{
	$ip_addresses = array();



	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

		if ( !is_string($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			// Log the value somehow, to improve the script!
			continue;
		}

		// Sometimes returns comma-delimited list of addresses!
		$x_forwarded_for = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$x_forwarded_for = array_map('trim', $x_forwarded_for);

		// Not using array_merge in order to preserve order (even though this is the fist type checked).
		// Better solutions welcome!
		foreach ( $x_forwarded_for as $x ) {
			$ip_addresses[] = $x;
		}

	}



	if(isset($_SERVER['HTTP_CLIENT_IP'])) {

		if ( !is_string($_SERVER['HTTP_CLIENT_IP']) ) {
			// Log the value somehow, to improve the script!
			continue;
		}

		$client_ip = explode(',', $_SERVER['HTTP_CLIENT_IP']);
		$client_ip = array_map('trim', $client_ip);

		foreach ( $client_ip as $c ) {
			$ip_addresses[] = $c;
		}

	}



	if ( isset($_SERVER['REMOTE_ADDR']) ) {

		if ( !is_string($_SERVER['REMOTE_ADDR']) ) {
			// Log the value somehow, to improve the script!
			continue;
		}

		$remote_addr = explode(',', $_SERVER['REMOTE_ADDR']);
		$remote_addr = array_map('trim', $remote_addr);

		foreach ( $remote_addr as $r ) {
			$ip_addresses[] = $r;
		}

	}



	if ( count($ip_addresses)==0 ) {

		return FALSE;

	} elseif ( $force_string===TRUE || ( $force_string===NULL && count($ip_addresses)==1 ) ) {

		return $ip_addresses[0];

	} else {

		return $ip_addresses;
	}

}



/**
 * Return an array containing the quoted, unquoted, plussed, and minussed portions of $text
 *
 * @author     Dotan Cohen
 * @version    2013-06-27
 *
 * @param string $text             The text to parse
 * @param bool   $combine_unquoted If TRUE, return string of unquoted text instead of array
 * @param bool   $combine_quoted   If TRUE, return string of quoted text instead of array
 *
 * @return array
 */
function separate_operator_text($text, $combine_unquoted=FALSE, $combine_quoted=FALSE)
{
	if ( !is_string($text) || !is_bool($combine_unquoted) || !is_bool($combine_quoted) ) {
		return NULL;
	}

	$output = array();
	$output['quoted'] = array();
	$output['unquoted'] = array();
	$output['plus'] = array();
	$output['minus'] = array();
	$output['unquoted_preliminary'] = array(); // Using subarray in $output to simplify un/quoted separation

	$text_parts = explode('"', $text);

	$quoted = FALSE;
	foreach ( $text_parts as $tp ) {
		$output_element = $quoted ? 'quoted' : 'unquoted_preliminary';
		$quoted = !$quoted;

		if ( trim($tp)!='' ) {
			$output[$output_element][] = trim($tp);
		}
	}

	foreach ( $output['unquoted_preliminary'] as $up ) {

		// TODO: Fix here, need to explode each $up on ' '

		if ( $up[0]=='+' ) {
			$output['plus'][] = substr($up, 1);
		} else if ( $up[0]=='-' ) {
			$output['minus'][] = substr($up, 1);
		} else {
			$output['unquoted'] = $up;
		}
	}

	unset($output['unquoted_preliminary']);

	if ( $combine_unquoted ) {
		$output['unquoted'] = implode(' ', $output['unquoted']);
	}

	if ( $combine_quoted ) {
		$output['quoted'] = implode(' ', $output['quoted']);
	}

	return $output;
}



/**
 * Return an associative array containing the headers of an $oauth request
 *
 * @author     Dotan Cohen
 * @version    2013-06-19
 *
 * @param OAuth $oauth The OAuth object for which a request has been made
 *
 * @return array
 */
function oauth_get_headers_array($oauth)
{
	if ( !($oauth instanceof OAuth) ) {
		return NULL;
	}

	$headers = array();
	$result_headers = explode("\r\n", $oauth->getLastResponseHeaders());

	foreach ( $result_headers as $rh ) {
		$pos = stripos($rh, ':');
		if ( $pos===FALSE ) {
			$headers[] = $rh;
		} else {
			$name  = trim(substr($rh, 0, $pos));
			$value = trim(substr($rh, $pos));
			$headers[$name] = $value;
		}
	}

	return $headers;
}



/**
 * Return an array containing the typical values of it's elements
 *
 * Different instances of an array will typically hold different values for each
 * of the defined (associative) elements. In order to reverse-engineer the format
 * of the array, one must have access to many 'typical values' for each element.
 * Thus, this function takes an array of 'typical arrays' and creates a master array
 * for which each element of the 'typical arrays' contains an array 'values' which
 * contain the values of the original arrays.
 *
 * @author     Dotan Cohen
 * @version    ????
 *
 * @param array $input An array containing 'typical arrays'.
 *
 * @return array
 */
function get_typical_results($input)
{
	// http://stackoverflow.com/questions/17194649/get-path-and-value-of-all-elements-in-nested-associative-array
	if ( !is_array($input) ) {
		return FALSE;
	}

	$typical_results = new stdClass();
	$rn = 1; // Result Number

	foreach ( $input as $element ) {
		if ( !is_array($elementi) ) {
			continue;
		}

		$pd = 0 // Path Depth
		$path = array();

		foreach ( $element as $f1=>$v1 ) {
			$path[] = 

			$typical_results[$k1][$rn] = $v1;
		}


		$rn += 1;
	}

	return $typical_results;
}



?>

<?php



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

		$ip_addresses[] = $_SERVER['HTTP_CLIENT_IP'];

	}



	if ( isset($_SERVER['REMOTE_ADDR']) ) {

		if ( !is_string($_SERVER['REMOTE_ADDR']) ) {
			// Log the value somehow, to improve the script!
			continue;
		}

		$ip_addresses[] = $_SERVER['REMOTE_ADDR'];

	}



	if ( count($ip_addresses)==0 ) {

		return FALSE;

	} elseif ( $force_string===TRUE || ( $force_string===NULL && count($ip_addresses)==1 ) ) {

		return $ip_addresses[0];

	} else {

		return $ip_addresses;
	}

}



?>

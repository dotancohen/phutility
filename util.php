<?php


/**
 * Send email via Amazon SES
 *
 * The AWSSDKforPHP library must be installed first!
 * http://aws.amazon.com/sdkforphp/
 *
 * @author     Dotan Cohen
 * @version    2013-11-03
 *
 * @param  string|array $to      The addresss(es) of the intended recipients of the mail
 * @param  string       $subject The subject of the mail
 * @param  string|array $message The text of the mail, or an array containing 'text' and 'html' elements.
 * @param  string       $to      The addresss of the sender of the mail
 * @param  string|array $cc      The addresss(es) to which copies of the mail should be sent
 * @param  string|array $bcc     The addresss(es) to which copies of the mail should be surreptitiously sent
 *
 * @return bool
 */
function send_email_ses($to, $subject, $message, $from, $cc=NULL, $bcc=NULL)
{
	$client = getAwsClient('SES');

	$addresses = array();
	$addresses['ToAddresses'] = is_array($to) ? $to : array($to);

	if ( $cc!==NULL ) {
		$addresses['CcAddresses'] = is_array($cc) ? $bcc : array($cc);
	}
	if ( $bcc!==NULL ) {
		$addresses['BccAddresses'] = is_array($bcc) ? $bcc : array($bcc);
	}
	if ( !is_array($message) ) {
		$message = array('text'=>$message);
	}

	$formatted = array(
		'Source' => $from,
		'Destination' => array(
			'ToAddresses' => $addresses['ToAddresses'],
			//'BccAddresses' => array($addresses['BccAddresses']),
			//'CcAddresses' => array($addresses['CcAddresses']),
		),
		'Message' => array(
			'Subject' => array(
				'Data' => $subject,
				'Charset' => 'UTF-8',
			),
			'Body' => array(
				'Text' => array(
					'Data' => $message['text'],
					'Charset' => 'UTF-8',
				),
				//'Html' => array(
					//// Data is required
					//'Data' => $message['html'],
					//'Charset' => 'UTF-8',
				//),
			),
		),
		//'ReplyToAddresses' => array('string', ... ),
		//'ReturnPath' => 'string',
	);

	if ( isset($addresses['BccAddresses']) ) {
		$formatted['Destination']['BccAddresses'] == $addresses['BccAddresses'];
	}
	if ( isset($addresses['CcAddresses']) ) {
		$formatted['Destination']['CcAddresses'] == $addresses['CcAddresses'];
	}
	if ( isset($message['html']) ) {
		$formatted['Message']['Body']['Html'] == $message['html'];
	}

	$result = $client->sendEmail($formatted);

	// TODO: Error handling
	return True;
}



/**
 * Return an Amazon AWS client
 *
 * The AWSSDKforPHP library must be installed first!
 * http://aws.amazon.com/sdkforphp/
 * Your AWS Key and Secret must be retrieved from here:
 * https://portal.aws.amazon.com/gp/aws/securityCredentials
 *
 * @author     Dotan Cohen
 * @version    2013-11-03
 *
 * @return bool
 */
function getAwsClient($service)
{
	require './vendor/autoload.php';
	$service = strtolower($service);

	switch($service){
		case('ses'):
			//use Aws\Ses\SesClient;
			$client = Aws\Ses\SesClient::factory(array(
						'key'    => '',
						'secret' => '',
						'region' => 'us-east-1',
			));
		break;
	}

	return $client;
}



/**
 * Send email via Amazon SES
 *
 * The AWSSDKforPHP library must be installed first!
 * http://aws.amazon.com/sdkforphp/
 * Note that this function refers to an outdated AWSSDK version.
 *
 * @author     Dotan Cohen
 * @version    2013-08-29
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
function send_email_ses_SDK1($to, $subject, $message, $from, $cc=NULL, $bcc=NULL)
{
	require_once('AWSSDKforPHP/sdk.class.php');
	require_once('AWSSDKforPHP/services/ses.class.php');

	$amazonSes = new AmazonSES();
	$addresses = array();

	$addresses['ToAddresses'] = is_array($to) ? $to : array($to);

	if ( $cc!==NULL ) {
		$addresses['CcAddresses'] = is_array($cc) ? $bcc : array($cc);
	}

	if ( $bcc!==NULL ) {
		$addresses['BccAddresses'] = is_array($bcc) ? $bcc : array($bcc);
	}

	$message_array = array(
		'Subject.Charset'   => 'UTF-8',
		'Body.Text.Charset' => 'UTF-8',
		'Subject.Data'      => $subject,
		'Body.Text.Data'    => $message
	);

	$response = $amazonSes->send_email($from, $addresses, $message_array);

	// TODO: Error handling. See Zim file for error examples.
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
 * Return an array containing the quoted, unquoted, plused, and minused portions of $text
 *
 * @author     Dotan Cohen
 * @version    2013-07-03
 *
 * @param string $text     The text to parse
 * @param array  $combine  Array of elements to combine into strings.
 *
 * @return array
 */
function separate_operator_text($text, $combine_unquoted=FALSE, $combine_quoted=FALSE)
{
	// TODO: Do not disregard lone +/-
	// TODO: Handle +/- before quoted text

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
		$bits = explode(' ', $up);
		$plus = '';
		$minus = '';
		$unquoted = '';

		foreach ( $bits as $bit ) {
			if ( $bit[0]=='+' ) {
				$plus .= substr($bit, 1) . ' ';
			} else if ( $up[0]=='-' ) {
				$minus .= substr($bit, 1) . ' ';
			} else {
				$unquoted .= $bit . ' ';
			}
		}

		if ( $plus!='' ) {
			$output['plus'][] = substr($plus, 0 ,-1);
		}
		if ( $minus!='' ) {
			$output['minus'][] = substr($minus, 0 ,-1);
		}
		if ( $unquoted!='' ) {
			$output['unquoted'][] = substr($unquoted, 0 ,-1);
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
 * Return an array containing the typical values of it's elements
 *
 * Different instances of an array will typically hold different values for each
 * of the defined (associative) elements. In order to reverse-engineer the format
 * of the array, one must have access to many 'typical values' for each element.
 * Thus, this function takes an array of 'typical arrays' and creates a master array
 * for which each element of the 'typical arrays' contains an array 'values' which
 * contain the values of the original arrays.
 *
 * http://stackoverflow.com/questions/17194649/get-path-and-value-of-all-elements-in-nested-associative-array
 *
 * @author     Dotan Cohen
 * @author     Jacob S
 * @version    ???? (Not done yet)
 *
 * @param array $input An array containing 'typical arrays'.
 *
 * @return array
 */
function get_typical_results($input)
{
	if ( !is_array($input) ) {
		return FALSE;
	}

	$typical_results = new stdClass();
	$rn = 1; // Result Number

	foreach ( $input as $element ) {
		if ( !is_array($elementi) ) {
			continue;
		}

		$pd = 0; // Path Depth
		$path = array();

		foreach ( $element as $f1=>$v1 ) {
			//$path[] = 

			$typical_results[$k1][$rn] = $v1;
		}


		$rn += 1;
	}

	return $typical_results;
}



?>

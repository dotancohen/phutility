<?php

/**
 * Return an array containing the input or transformed from the input, to ensure that foreach can be run on the input
 * 
 * @author     Dotan Cohen
 * @version    2016-07-28
 *
 * @param $input
 * @return array
 */
function ensureArray($input)
{
	if ( is_array($input) ) {
		return $input;
	}

	if ($input instanceof \stdClass) {
		return json_decode(json_encode($input), TRUE);
	}

	return array($input);
}



/**
 * Return a desired HTTP header
 *
 * Accepts pseudo-headers: Protocol and RespCode
 * Known Issue: If multiple headers share a key, returns only the first
 *
 * @author     Dotan Cohen
 * @version    2017-03-20
 *
 * @param $headers array Array of HTTP headers
 * @param $header string Name of desired HTTP header, or pseudo-header Protocol or RespCode
 *
 * @return null|string
 */
function getHeader(array $headers, $header)
{
	// TODO: Support pseudo-headers ProtocolType ProtocolVersion RespDescription Charset

	$header = trim(strtolower($header));

	foreach ( $headers as $item ) {
		$parts = explode(':', $item, 2);

		// Support pseudo-headers
		if ( count($parts) < 2 ) {
			if ( substr($parts[0],0,4)=='HTTP' ) {

				$parts = explode(' ', $item);
				if ( 2<=count($parts) ) {
					switch ($header) {
						case 'protocol':
							return $parts[0];
							break;
						case 'respcode':
							return $parts[1];
							break;
					}
				}
			}

			continue;
		}

		$key = trim(strtolower($parts[0]));
		$val = trim($parts[1]);

		if ( $key==$header ) {
			return $val;
		}
	}

	return NULL;
}


/**
 * Return the HTTP request parameters normalized
 *
 * @author     Dotan Cohen
 * @version    2017-07-10
 *
 * @return array
 */
function getRequestNormalized()
{
	static $request;

	if ( is_null($request) ) {
		$request = array();
		// TODO: Separate _GET _POST _COOKIE and maybe _FILE
		foreach ( $_REQUEST as $k => $v ) {
			$request[strtolower($k)] = mb_convert_encoding($v, 'UTF-8', 'UTF-8');
		}
	}

	return $request;
}


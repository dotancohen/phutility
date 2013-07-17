<?php



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



?>

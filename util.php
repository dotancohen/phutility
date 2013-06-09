<?php

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
	$url="simplesniff.com/ip";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}



?>

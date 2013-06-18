<?php


/**
 * Separate a string into the name and value pair that it represents
 *
 * @author     Dotan Cohen
 * @version    2013-06-18
 *
 * @param $input              string       The input string to separate
 * @param $separators         array|string The separators to separate on
 * @param $comment_indicators array        Legal comment indicators
 *
 * @return array|bool An array containing the elements 'name' and 'value'
 */
function separate_name_value($input, $separators, $comment_indicators=array())
{
	if ( !is_string($input) ) {
		return NULL;
	}

	if ( !is_string($separator) && !is_array($separator) ) {
		return NULL;
	}

	if ( !is_array($separator) ) {
		$separator = array($separator);
	}

	$aConfigurationSeparators = array(' ', "\t");

	foreach ($input as $i) {
		$i = trim($i);
		if ( substr($i, 0 ,1)=='#' || substr($i, 0, 2)=='//' || substr($i, 0, 1)==';') {
			continue;
		}
		if ( strpos($i, ' ')===FALSE && strpos($i, "\t")===FALSE ) {
			continue;
		}

		// All characters up to (not including) first space
		$sConfigurationName = trim(substr($i, 0, Phpfox::getService('discoapi.configuration')->strpos_array($i, $aConfigurationSeparators)));

		// All characters after (not including) first space
		$sConfigurationValue = trim(substr($i, Phpfox::getService('discoapi.configuration')->strpos_array($i, $aConfigurationSeparators)));

		define($sConfigurationName, $sConfigurationValue);
	}

	return TRUE;
}



/**
 * Get the first position of any of the specified characters
 *
 * @author     Dotan Cohen
 * @version    2013-06-18
 *
 * @param $input   string       The input string to find the needles in
 * @param $needles array|string The needles to search on
 * @param $offset  int          The offset from the beginning of $input to start searching on
 *
 * @return int|bool The position of the first needle
 */
function strpos_array($input, $needles, $offset=0)
{
	$chr = array();
	foreach($needles as $needle) {
		$res = strpos($input, $needle, $offset);
		if ($res !== FALSE) $chr[$needle] = $res;
	}

	if (empty($chr)) {
		return FALSE;
	}

	return min($chr);

}


?>

<?php



/**
 * Escape values destined for Solr
 *
 * @author     Dotan Cohen
 * @version    2013-05-30
 *
 * @param value to be escaped. Valid data types: string, array, int, float, bool
 * @return Escaped string, NULL on invalid input
 */
function solr_escape($str)
{
	if ( is_array($str) ) {
		foreach ( $str as &$s ) {
			$s = solr_escape($s);
		}

		return $str;
	}

	if ( is_int($str) || is_float($str) || is_bool($str) ) {
		return $str;
	}

	if ( !is_string($str) ) {
		return NULL;
	}

	$str = addcslashes($str, "+-!(){}[]^\"~*?:\\");
	$str = str_replace("&&", "\\&&", $str);
	$str = str_replace("||", "\\||", $str);

	return $str;
}





?>

<?php

/**
 * Ensure that all necessary array elements are present and not empty.
 *
 * Pass to this function the array to check in the first parameter, then
 * in each additional parameter pass in strings which represent array
 * elements which must be present and not empty.
 *
 * @author     Dotan Cohen
 * @version    2013-05-30
 *
 * @param Array to be checked
 * @param All elements that must exist and not be empty strings.
 * @return bool
 */
function ensure_fields($consideration)
{
	$args = func_get_args();
	$pass = 0;
	foreach ( $args as $a ) {
		if ( $pass++ == 0 ) {
			continue;
		}
		if ( !isset($consideration[$a]) || $consideration[$a]=='' ) {
			return FALSE;
		}
	}

	return TRUE;
}


?>

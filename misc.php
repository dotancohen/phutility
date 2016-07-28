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


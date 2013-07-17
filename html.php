<?php



/**
* Output the <options> of a select statement and optionally preselect one
*
* @author Dotan Cohen
* @version 2013-06-30
*
* @param array   $data        Associative array to be converted to <options>
* @param string  $quote_char  Character to use to quote values. Must be " or '
* @param string  $selected    Default value to select
*
* @return TRUE
*/
function output_select_options($data, $quote_char='"', $selected=NULL)
{
	if ( !is_array($data) || !in_array($quote_char, array('"', "'")) ) {
		return NULL;
	}

	foreach ( $data as $f=>$v) {
		$s = $f==$selected ? " selected={$quote_char}selected{$quote_char}" : '';
		echo "<option value={$quote_char}{$f}{$quote_char}{$s}>{$v}</option>";
	}

	return TRUE;
}



?>

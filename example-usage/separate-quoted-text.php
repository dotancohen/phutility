<?php

$input_strings = array();

$input_strings[] = <<<STRING
The bird said "Nevermore" and then flew off!
STRING;

$input_strings[] = <<<STRING
"Nevermore" was all the bird could say.
STRING;

$input_strings[] = <<<STRING
The bird said "Nevermore" and then "flew off!
STRING;

$input_strings[] = <<<STRING
"Nevermore" was all the bird "could say.
STRING;

$input_strings[] = <<<STRING
The bird said "Nevermore" and then flew off!"
STRING;

$input_strings[] = <<<STRING
"Nevermore" was all the bird could say."
STRING;


foreach ( $input_strings as $is ) {

	echo "\n\n\nProcessing {$is}\n";
	$processed = separate_quoted_text($is, TRUE, TRUE);


	echo "Unquoted bits:\n";
	if ( is_array($processed['unquoted']) ) {
		foreach ( $processed['unquoted'] as $p ) {
			echo "-".$p."\n";
		}
	} else {
		echo "-".$processed['unquoted']."\n";
	}


	echo "\nQuoted bits:\n";
	if ( is_array($processed['quoted']) ) {
		foreach ( $processed['quoted'] as $q ) {
			echo "-".$q."\n";
		}
	} else {
		echo "-".$processed['quoted']."\n";
	}

}



?>

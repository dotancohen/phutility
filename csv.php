<?php

// Great for converting the output of a database query to a CSV file

// http://www.creativyst.com/Doc/Articles/CSV/CSV01.htm
// https://tools.ietf.org/html/rfc4180

/*
TODO:
	getCsvFile(): Handle arbitrary streams, not simply return $input
	Detect rows longer than the header
	Do not escape fields that do not need escaping
	Directly handle the results of a PDO statement after execute()
*/


class CsvUtils {


	/**
	 * Return a legal CSV file
	 * 
	 * @param $input Array of associative arrays
	 * @return string
	 */
	private static function getCsvFile($input, $separator=',')
	{
		$output = '';
		$header = TRUE;

		foreach ( $input as $row ) {
			$output .= getCsvRow($row, $separator, $header);
			$header = FALSE;
		}

		return $output;
	}



	/**
	 * Return a legal CSV row with optional header
	 * 
	 * @param $row
	 * @param $r
	 * @param $header
	 * @return string
	 */
	private static function getCsvRow($row, $separator=',', $header=FALSE)
	{
		if ( $header ) {
			$pre = implode($separator, array_map(array(__CLASS__, 'escapeCsvField'), array_keys($row))) . "\n";
		} else {
			$pre = '';
		}

		return $pre . implode($separator, array_map(array(__CLASS__, 'escapeCsvField'), $row)) . "\n";
	}

	

	/**
	 * Return a legal CSV field
	 *
	 * @param $input
	 * @return string
	 */
	private static function escapeCsvField($input)
	{
		return '"' . str_replace('"', '""', $input) . '"';
	}


}


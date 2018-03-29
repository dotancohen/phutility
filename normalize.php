<?php

namespace Phutility;


class Normalize {


	/** Return a normalized Israeli phone number
	 *
	 * @param string $phone_number Phone number
	 * @param bool $abroad Format the phone number for use outside Israel
	 * @return string|null Formatted phone number, null on invalid input
	 */
	public static function phoneIsrael($phone_number, $abroad=false)
	{
		$s = preg_replace('/[x\D]/iu', '', $phone_number);


		// Account for phone extension
		$parts = explode('x', strtolower($s));
		$s = $parts[0];


		// Normalize
		if ( substr($s, 0, 3)==='972' ) {
			$s = '0' . substr($s, 3);
		}


		// Check length
		if ( strlen($s)==(10) ) {
			if ( $s[1]!=='5' ) {
				return null;
			}

		} else if ( strlen($s)==(9) ) {
			if ( $s[1]==='5' ) {
				return null;
			}

		} else {
			return null;
		}


		// Format
		$s = substr_replace($s, '.', -7, 0);
		$s = substr_replace($s, '.', -4, 0);


		// Replace extension
		if ( 1<count($parts) ) {
			$ext = preg_replace('/[\D]/u', '', $parts[1]);
			$s = $s . ' Ext ' . $ext;
		}


		// Outside Israel
		if ( $abroad ) {
			$s = '+972.'.substr($s, 1);
		}

		return $s;
	}


}


<?php

namespace Phutility;


class Normalize {


	const FORMAT_DATE = 'Y-m-d';
	const FORMAT_TIME = 'H:i:s';
	const FORMAT_DATETIME = 'Y-m-d H:i:s';


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


	/**
	 * Normalize a Date
	 *
	 * @param DateTime|int|string $date
	 * @return null|string
	 */
	public static function normalizeDate($date)
	{
		return self::_normalizeFormat($date, self::FORMAT_DATE);
	}


	/**
	 * Normalize a Time
	 *
	 * @param DateTime|int|string $date
	 * @return null|string
	 */
	public static function normalizeTime($date)
	{
		return self::_normalizeFormat($date, self::FORMAT_TIME);
	}


	/**
	 * Normalize a DateTime
	 *
	 * @param DateTime|int|string $date
	 * @return null|string
	 */
	public static function normalizeDateTime($date)
	{
		return self::_normalizeFormat($date, self::FORMAT_DATETIME);
	}


	/**
	 * Normalize a DateTime
	 *
	 * @param DateTime|int|string $date
	 * @return null|string
	 */
	protected static function _normalizeFormat($date, $format)
	{
		if ($date instanceof \DateTime) {
			return $date->format($format);
		}

		if ( is_numeric($date) && 99999999<(int)$date ) { // Consider dates in format "Ymd" to not be UNIX timestamps
			return date($format, $date);
		}

		return $date ? date($format, strtotime($date)) : null;
	}


}


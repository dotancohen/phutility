<?php

/*
 * Class for easily debugging PDO statements
 *
 * (c) 2013 Dotan Cohen
 * This computer code file is the proprietary intellectual
 * property of Dotan Cohen. Copying or modifying the file,
 * in part or in whole, is strictly forbidden.
 *
 *
 * TODO
 * Add remaining PDO methods
 *
 *
 * KNOWN ISSUES
 * Only supports the most basic PDO functionality
 *
 *
 * @copyright  [DOTAN COHEN]
 * @author     Dotan Cohen
 * @package    PDOops
 * @version    $Id: pdoops.php 2013-03-13 15:45$
 *
 */

class PDOops extends PDO
{

	private $_pdo_connection;
	private $_statement;
	private $_bindValues;


	public function __construct($dsn, $username=NULL, $password=NULL, $driver_options=NULL)
	{
		$this->_bindValues = array();

		$_pdo_connection = parent::__construct($dsn, $username, $password, $driver_options);
		return $_pdo_connection;
	}


	public function prepare($statement, $driver_options=NULL)
	{
		$this->_statement = $statement;
		return parent::prepare($statement, $driver_options);
	}


	public function bindValue($parameter, $value, $data_type=NULL)
	{
		$this->_bindValues[$parameter] = $value;
		return parent::bindValue($parameter, $value, $data_type);
	}


	public function getPseudoSql()
	{
		$pseudoSql = $this->_statement;

		foreach ( $this->_bindValues as $f=>$v ) {
			str_replace('{$f}', '{$v}', $pseudoSql);
		}

		return $pseudoSql;
	}

}

?>

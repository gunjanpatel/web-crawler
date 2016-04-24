<?php

namespace Crawler\Database;

/**
 * Database class for PDO and mysql drivers
 */
class Database
{
	/**
	 * Static instance of PDO Object
	 *
	 * @var  object
	 */
	private static $objInstance;

	/**
	 * Class Constructor - Create a new database connection if one doesn't exist
	 * Set to private so no-one can create a new instance via ' = new DB();'
	 */
	private function __construct(){}

	/**
	 * Like the constructor, we make __clone private so nobody can clone the instance
	 *
	 * @return  void
	 */
	private function __clone(){}

	/**
	 * Returns DB instance or create initial connection
	 *
	 * @throws  Exception Missing config file
	 *
	 * @return  object  PDO Object
	 */
	public static function getInstance()
	{
		if (!self::$objInstance)
		{
			$filePath = __DIR__ . '/../../config/db.json';

			if (!file_exists($filePath))
			{
				throw new Exception("Please create configuration file or make sure it's readable.");
			}

			$configFile = json_decode(file_get_contents($filePath));

			$port = (isset($configFile->port)) ? ';port=' . $configFile->port : '';
			$dsn  = $configFile->driver . ':host=' . $configFile->host . $port . ';dbname=' . $configFile->schema;

			self::$objInstance = new \PDO($dsn, $configFile->username, $configFile->password);
			self::$objInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}

		return self::$objInstance;

	}

	/**
	 * Passes on any static calls to this class onto the singleton PDO instance
	 *
	 * @param   string  $chrMethod     Method name
	 * @param   array   $arrArguments  Use method array name
	 *
	 * @return  mix                 PDO Method object
	 */
	final public static function __callStatic($chrMethod, $arrArguments)
	{
		$objInstance = self::getInstance();

		return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);

	}
}

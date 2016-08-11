<?php
/**
 * Database Query Helper, Simple, Powerful.
 * 
 * @package   SDB
 * @author    ShadowMan <shadowman@shellboot.com>
 * @copyright Copyright (C) 2016 ShadowMan
 * @license   MIT License
 * @version   Develop: 1.0.0
 * @link      https://github.com/JShadowMan/db
 */
namespace SDB;

class Helper {
    /**
     * server information
     * @var array
     */
    private static $_server = array();

    private static $_driver = null;

    /**
     * Helper constructor
     * 
     * @param string $adapter
     * @param string $tablePrefix
     */
    public function __construct($adapter, $tablePrefix) {
        
    }

    public static function server($host, $port, $user, $password, $database, $charset = 'utf8') {
        if (self::$_driver === null) {
            self::initDriver();
        }
        
        $args = array_map(function($value) {
            if (!is_string($value)) {
                return strval($value);
            }
            return $value;
        }, func_get_args());
    }

    private static function initDriver() {
        $loaded = get_loaded_extensions();

        if (in_array('PDO', $loaded) && extension_loaded('PDO')) {
            $filtered = array_filter($loaded, function($value) {
                return strpos($value, 'pdo_') === 0;
            });

            # PDO module support is not enabled, check mysql, oracle and more
            if (empty($filtered)) {
                $filtered = array_filter($loaded, function($value) {
                    return in_array($value, array('mysqli', 'oci'));
                });
            }

            # Database module support is not enabled. raise Exception
            if (empty($filtered)) {
                throw new \Exception('', '');
            }
        }
    }

    # Database: MySQL
    const ADAPTER_MYSQL      = 'MySQL';

    # Database: SQL Server
    const ADAPTER_SQL_SERVER = 'SQL_SERVER';

    # Database: Oracle
    const ADAPTER_ORACLE     = 'ORACLE';

    # Database: SQLite
    const ADAPTER_SQLITE     = 'SQLITE';

    # CRUD Operator: Insert
    const OPERATOR_INSERT    = 'INSERT';

    # CRUD Operator: Select
    const OPERATOR_SELECT    = 'SELECT';

    # CRUD Operator: Update
    const OPERATOR_UPDATE    = 'UPDATE';

    # CRUD Operator: Delete
    const OPERATOR_DELETE    = 'DELETE';

    # Operator: Change
    const OPERATOR_CHANGE    = 'CHANGE';
}

?>
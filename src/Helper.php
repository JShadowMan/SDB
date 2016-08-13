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

    /**
     * driver information
     * 
     * @var array
     */
    private static $_driver = null;

    /**
     * adapter instance
     * 
     * @var SDB\Abstracts\Adapter
     */
    private $_adapter = null;

    /**
     * table prefix
     * 
     * @var string
     */
    private $_tablePrefix = null;

    /**
     * Helper constructor
     * 
     * @param string $adapter
     * @param string $tablePrefix
     */
    public function __construct($tablePrefix, $adapter = null) {
        if (!in_array($adapter, array(self::ADAPTER_MYSQL, 
                self::ADAPTER_ORACLE, self::ADAPTER_SQL_SERVER, self::ADAPTER_SQLITE))) {
            throw new \Exception('adapter not found', 1996);
        }

        if (!is_string($tablePrefix)) {
            throw new \Exception('table prefix except string', 1996);
        }
        $this->_tablePrefix = $tablePrefix;

        if ($adapter === null) {
            if (in_array(self::ADAPTER_MYSQL, self::$_driver)) {
                $adapter = self::ADAPTER_MYSQL;
            } else {
                $adapter = current(self::$_driver);
            }
        }

        $adapter = "SDB\\Adapter\\{$adapter}";
        if (!call_user_func(array($adapter, 'avaliable'))) {
            throw new \Exception('adapter is not avaliable', 1996);
        } else {
            $this->_adapter = new $adapter;
        }
        var_dump($this);
    }

    /**
     * server information
     * 
     * @param string $host
     * @param string|int $port
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $charset
     */
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

        self::$_server[] = array_merge($args, array( '__connectable__' => false ));
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
                throw new \Exception('database module support is not enabled', 1996);
            }

            # Create driver => adapter mapping
            foreach ($filtered as $driver) {
                switch ($driver) {
                    case 'pdo_mysql':
                    case 'mysqli':   self::$_driver[$driver] = self::ADAPTER_MYSQL; break;
                    case 'pdo_oci':
                    case 'oci':      self::$_driver[$driver] = self::ADAPTER_ORACLE; break;
                    default: throw new \Exception('fatal error', 1996);
                }
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
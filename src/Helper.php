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
     * strict mode flag
     * 
     * @var boolean
     */
    private static $_adapterStrictMode = true;

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
        if (!in_array($adapter, array_values(self::$_driver))) {
            throw new \Exception('SDB: adapter or driver invalid', 1996);
        }

        if (!is_string($tablePrefix)) {
            throw new \Exception('SDB: table prefix except string', 1996);
        }
        $this->_tablePrefix = $tablePrefix;

        # default using mysql, if mysql is disable, that using first database adapter
        if ($adapter === null) {
            if (in_array(self::ADAPTER_MYSQL, self::$_driver)) {
                $adapter = self::ADAPTER_MYSQL;
            } else {
                $adapter = current(self::$_driver);
            }
        }

        # if adapter avaliable, creating the instance
        $adapter = "SDB\\Adapter\\{$adapter}";
        if (!call_user_func(array(($this->_adapter = new $adapter($this->_tablePrefix)), 'avaliable'))) {
            unset($this->_adapter);
            throw new \Exception('SDB: adapter is not avaliable', 1996);
        }
    }

    /**
     * disable strict mode for adapter mapping
     */
    public static function disableStrictMode() {
        self::$_adapterStrictMode = true;
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
        if (count($args) == 5) {
            $args[] = $charset;
        }

        self::$_server[] = array_merge($args, array( '__connectable__' => false ));
    }

    /**
     * private: initialize driver
     * create driver => adapter mapping
     * 
     * @throws \Exception
     */
    private static function initDriver() {
        $loaded = get_loaded_extensions();

        if (in_array('PDO', $loaded) && extension_loaded('PDO')) {
            $filtered = array_filter($loaded, function($value) {
                return strpos($value, 'pdo_') === 0 || in_array($value, array('mysqli', 'oci'));
            });

            # Database module support is not enabled. raise Exception
            if (empty($filtered)) {
                throw new \Exception('SDB: database module support is not enabled', 1996);
            }

            # Create driver => adapter mapping
            foreach ($filtered as $driver) {
                switch ($driver) {
                    case 'pdo_mysql' : self::$_driver[$driver] = self::ADAPTER_PDO_MYSQL;  break;
                    case 'mysqli'    : self::$_driver[$driver] = self::ADAPTER_MYSQL;      break;
                    case 'pdo_oci'   : self::$_driver[$driver] = self::ADAPTER_PDO_ORACLE; break;
                    case 'oci'       : self::$_driver[$driver] = self::ADAPTER_ORACLE;     break;
                    case 'pgsql'     :
                    case 'pdo_pgsql' : self::$_driver[$driver] = self::ADAPTER_PGSQL;  break;
                    case 'sqlite'    :
                    case 'pdo_sqlite': self::$_driver[$driver] = self::ADAPTER_SQLITE;  break;
                    default: if (self::$_adapterStrictMode === true) {
                        throw new \Exception("SDB: fatal error, driver({$driver}) invalid.\n" . serialize($filtered), 1996);
                    }
                }
            }
        }
    }

    /**
     * return Query instance
     * 
     * @return \SDB\Query
     */
    public function builder() {
        return new Query($this->_adapter);
    }

    /**
     * SQL Basic Syntax: SELECT
     */
    public function select() {
        return $this->builder()->select(func_get_args());
    }

    /**
     * SQL Basic Syntax: UPDATE
     *
     * @param string $table
     */
    public function update($table) {
        return $this->builder()->update($table);
    }

    /**
     * SQL Basic Syntax: INSERT
     *
     * @param string $table
     */
    public function insert($table) {
        return $this->builder()->insert($table);
    }

    /**
     * SQL Basic Syntax: DELETE
     *
     * @param string $table
     */
    public function delete($table) {
        return $this->builder()->delete($table);
    }

    /**
     * execute query
     * 
     * @param string|SDB\Query $table
     */
    public function query($query) {
        $query = trim($query instanceof Query ? $query->__toString() : $query);

        call_user_func_array(array($this->_adapter, 'connect'), self::$_server[array_rand(self::$_server, 1)]);
        return $this->_adapter->query($query);
    }

    public function prepare() {
        
    }

    public function functions($function, $args = null) {
        
    }

    # Database: MySQL
    const ADAPTER_PDO_MYSQL  = 'PDO_MySQL';

    # Database: MySQL, PDO
    const ADAPTER_MYSQL      = 'MySQL';

    # Database: SQL Server
    const ADAPTER_SQL_SERVER = 'SQL_SERVER';

    # Database: Oracle
    const ADAPTER_PDO_ORACLE = 'PDO_ORACLE';

    # Database: Oracle, PDO
    const ADAPTER_ORACLE     = 'ORACLE';

    # Database: SQLite
    const ADAPTER_SQLITE     = 'SQLITE';

    const ADAPTER_PGSQL      = 'PGSQL';

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

    # Data Type: DEFAULT
    const DATA_DEFAULT       = array('\x44\x45\x46\x41\x55\x4C\x54');

    #Data Type: NULL
    const DATA_NULL          = array('\x4E\x55\x4C\x4C');

    # Sort Type: DESC
    const ORDER_DESC         = 'DESC';

    # Sort Type: ASC
    const ORDER_ASC          = 'ASC';

    # Conjunction: AND
    const CONJUNCTION_AND    = 'AND';

    # Conjunction: OR
    const CONJUNCTION_OR     = 'OR';

    const JOIN_INNER         = 'INNER JOIN';

    const JOIN_LEFT          = 'LEFT JOIN';

    const JOIN_RIGHT          = 'RIGHT JOIN';
}

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

    private $_preQueryPool = array();

    /**
     * Helper constructor
     * 
     * @param string $adapter
     * @param string $tablePrefix
     */
    public function __construct($tablePrefix, $adapter = null) {
        if (!is_string($tablePrefix)) {
            throw new \Exception('SDB: table prefix except string', 1996);
        }
        $this->_tablePrefix = $tablePrefix;

        # default using mysql adapter, if mysql is disable, that throw Exception
        if ($adapter == null) {
            if (in_array(self::ADAPTER_MYSQL, self::$_driver)) {
                $adapter = self::ADAPTER_MYSQL;
            } else {
                throw new \Exception('SDB: default database adapter(MySQL) not found', 1996);
            }
        }

        if (!in_array($adapter, array_values(self::$_driver))) {
            throw new \Exception('SDB: adapter or driver invalid', 1996);
        }

        # if adapter avaliable, creating the instance
        $adapter = "SDB\\Adapter\\{$adapter}";
        if (!call_user_func(array(($this->_adapter = new $adapter($this->_tablePrefix)), 'available'))) {
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
        }, get_defined_vars());

        self::$_server[] = array_merge($args, array( '__connectable__' => false ));
    }

    public static function cleanServer() {
        return array_splice(self::$_server, 0, count(self::$_server));
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
                    case 'pdo_mysql'  : self::$_driver[$driver] = self::ADAPTER_PDO_MYSQL;  break;
                    case 'mysqli'     : self::$_driver[$driver] = self::ADAPTER_MYSQL;      break;
                    case 'pdo_oci'    : self::$_driver[$driver] = self::ADAPTER_PDO_ORACLE; break;
                    case 'oci'        : self::$_driver[$driver] = self::ADAPTER_ORACLE;     break;
                    case 'pgsql'      : self::$_driver[$driver] = self::ADAPTER_PGSQL;      break;
                    case 'pdo_pgsql'  : self::$_driver[$driver] = self::ADAPTER_PDO_PGSQL;  break;
                    case 'sqlite'     : self::$_driver[$driver] = self::ADAPTER_SQLITE;     break;
                    case 'pdo_sqlite' : self::$_driver[$driver] = self::ADAPTER_SQLITE;     break;
                    default: if (self::$_adapterStrictMode === true) {
                        throw new \Exception("SDB: fatal error, driver({$driver}) invalid.\n", 1996);
                    }
                }
            }
        }
    }

    public function lastInsertId() {
        return $this->_adapter->lastInsertId();
    }

    /**
     * return Query instance
     * 
     * @return \SDB\Query
     */
    public function builder() {
        return new Query($this->_adapter, $this);
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
    public function delete($tables, $using = null) {
        return $this->builder()->delete($tables, $using);
    }

    /**
     * execute query
     * 
     * @param string|SDB\Query $table
     */
    public function query($query) {
        $query = trim($query instanceof Query ? $query->__toString() : $query);

        $this->connect();
        return $this->_adapter->query($query);
    }

    public function fetchAssoc($keys = null) {
        return $this->_adapter->fetchAssoc($keys);
    }

    public function fetchAll() {
        return $this->_adapter->fetchAll();
    }

    public function affectedRows() {
        return $this->_adapter->affectedRows();
    }

    public function connect() {
        return call_user_func_array(array($this->_adapter, 'connect'), self::$_server[array_rand(self::$_server, 1)]);
    }

    public function serverInfo() {
        return $this->_adapter->serverInfo();
    }

    public function reset() {
        $this->_adapter->reset();
    }

    public function seek($index) {
        $this->_adapter->seek(is_int($index) ? $index : intval($index));
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

    # Database: PostgreSQL
    const ADAPTER_PGSQL      = 'PGSQL';

    # Database: PostgreSQL, PDO
    const ADAPTER_PDO_PGSQL  = 'PDO_PGSQL';

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
    const DATA_DEFAULT       = '\x44\x45\x46\x41\x55\x4C\x54';

    #Data Type: NULL
    const DATA_NULL          = '\x4E\x55\x4C\x4C';

    # Sort Type: DESC
    const ORDER_DESC         = 'DESC';

    # Sort Type: ASC
    const ORDER_ASC          = 'ASC';

    # Conjunction: AND
    const CONJUNCTION_AND    = 'AND';

    # Conjunction: OR
    const CONJUNCTION_OR     = 'OR';

    # Join: INNER JOIN
    const JOIN_INNER         = 'INNER JOIN';

    # Join: LEFT JOIN
    const JOIN_LEFT          = 'LEFT JOIN';

    # Join: RIGHT JOIN
    const JOIN_RIGHT          = 'RIGHT JOIN';
}

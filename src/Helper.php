<?php
namespace Db;

/**
 * @author ShadowMan
 */

// get_loaded_extensions()
// extension_loaded()
class Helper {
    /**
     * store server infomation
     * consisted of SERVER_ADDR, SERVER_PORT, USERNAME, PASSWORD, DATABASE, TABLE, TABLE_PREFIX
     * 
     * @var array
     */
    private static $_server = array();

    /**
     * store server adapter infomation
     * 
     * @var \Db\_Abstract\Abstract_Adapter
     */
    private $_adapter = null;

    /**
     * store table prefix 
     * 
     * @example 'table.<TABLE_NAME>'
     * @var string
     */
    private $_prefix  = null;

    private $_instancePool = array();

    /**
     * 
     * @param string $adapter
     * @param string $prefix
     */
    public function __construct($adapter, $prefix = null) {
        if (!in_array($adapter, array(self::DB_ADAPTER_MYSQL, self::DB_ADAPTER_ORACLE, self::DB_ADAPTER_SQL_SERVER, self::DB_ADAPTER_SQLITE))) {
            throw new \Exception('Adapter Not Defined.', 1);
        }
        if (!is_string($prefix)) {
            throw new \Exception('table prefix is not string', 2);
        }

        switch ($adapter) {
            case self::DB_ADAPTER_MYSQL: $this->_adapter = new Adapter\Adapter_MySQL(self::$_server); break;
            case self::DB_ADAPTER_ORACLE: 
            case self::DB_ADAPTER_SQL_SERVER:
            case self::DB_ADAPTER_SQLITE:
            default:
                throw new \Exception('Adapter Class Not Defined.', 1);
        }

        $this->_prefix = $prefix;
    }

    /**
     * create instance by factory
     * 
     * @param string $adapter
     * @param string $prefix
     * @param string $connect
     */
    public static function factory($adapter, $prefix = null, $connect = false) {
        $helper = new Helper($adapter, $prefix);

        return $connect ? $helper->connect() : $helper;
    }

    public static function server($host, $port, $user, $password, $database, $mutliServer = false) {
        self::$_server = array_merge(($mutliServer) ? self::$_server : array(), array(
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'database' => $database
        ));
    }

    public static function getServer() {
        return empty(self::$_server) ? null : self::$_server;
    }

    public function connect() {
        $this->_adapter->connect();
        return $this;
    }

    /**
     * 
     * @return \Db\_Abstract\Abstract_Adapter
     */
    public function getAdapter() {
        return $this->_adapter;
    }

    public function getPrefix() {
        return $this->_prefix;
    }

    private function builder() {
        return new Query($this->_adapter, $this->_prefix);
    }

    public function select() {
        return self::builder()->select(func_get_args());
    }

    public function update($table) {
        return self::builder()->update($table);
    }

    public function insert($table) {
        return self::builder()->insert($table);
    }

    public function delete($table) {
        return self::builder()->delete($table);
    }

    public function change($table) {
        return self::builder()->change($table);
    }

    # Database: MySQL
    const DB_ADAPTER_MYSQL      = 'MySQL';

    # Database: SQL Server
    const DB_ADAPTER_SQL_SERVER = 'SQL_SERVER';

    # Database: Oracle
    const DB_ADAPTER_ORACLE     = 'ORACLE';

    # Database: SQLite
    const DB_ADAPTER_SQLITE     = 'SQLITE';

    # CRUD Operator: Insert
    const DB_OPERATOR_INSERT    = 'INSERT';

    # CRUD Operator: Select
    const DB_OPERATOR_SELECT    = 'SELECT';

    # CRUD Operator: Update
    const DB_OPERATOR_UPDATE    = 'UPDATE';

    # CRUD Operator: Delete
    const DB_OPERATOR_DELETE    = 'DELETE';

    # Operator: Change
    const DB_OPERATOR_CHANGE    = 'CHANGE';
}

?>